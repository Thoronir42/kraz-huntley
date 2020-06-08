<?php declare(strict_types=1);

namespace SeStep\Executives\Validation;

use Nette\InvalidArgumentException;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Processor;
use Nette\Schema\Schema;
use Nette\Schema\ValidationException;
use SeStep\Executives\Execution\Action;
use SeStep\Executives\Execution\Condition;
use SeStep\Executives\Execution\ExecutivesLocator;
use SeStep\Executives\Model\ActionData;
use SeStep\Executives\Model\ConditionData;

class ExecutivesValidator
{
    /** @var Processor */
    private $processor;

    /** @var string */
    private $pathPrefix;

    /** @var ExecutivesLocator */
    private $executivesLocator;

    public function __construct(ExecutivesLocator $locator, string $pathPrefix = '')
    {
        $this->executivesLocator = $locator;
        $this->pathPrefix = $pathPrefix;
    }

    public function withPath(string $path)
    {
        $prefix = ($this->pathPrefix ? $this->pathPrefix . '.' : '') . $path;
        return new self($this->executivesLocator, $prefix);
    }

    /**
     * @param ActionData|array $actionData array must contain offsets 'type': string, params: array
     *
     * @return ValidationErrorCollection
     */
    public function validateActionData($actionData): ValidationErrorCollection
    {
        if ($actionData instanceof ActionData) {
            $type = $actionData->getType();
            $params = $actionData->getParams();
        } else {
            if (!isset($actionData['type'])) {
                return new ValidationErrorCollection($this->prefixErrors([
                    'type' => new ParamValidationError('exe.validation.missingField'),
                ], $this->pathPrefix));
            }

            $type = $actionData['type'];
            $params = $actionData['params'] ?? [];
            if (!is_array($params)) {
                return new ValidationErrorCollection($this->prefixErrors([
                    $this->path('params') => new ParamValidationError('exe.validation.unexpectedTypeInField'),
                ], $this->pathPrefix));
            }
        }

        return $this->validateActionParams($type, $params);
    }

    /**
     * @param ConditionData|array $conditionData
     * @return ValidationErrorCollection
     */
    public function validateConditionData($conditionData): ValidationErrorCollection
    {
        if ($conditionData instanceof ConditionData) {
            $type = $conditionData->getType();
            $params = $conditionData->getParams();
        } else {
            $type = $conditionData['type'] ?? '';
            $params = $conditionData['params'] ?? [];
        }

        return $this->validateConditionParams($type, $params);
    }

    /**
     * @param string $type
     * @param array $params
     * @param bool $normalize
     *
     * @return ValidationErrorCollection
     */
    public function validateActionParams(
        string $type,
        array &$params,
        bool $normalize = false
    ): ValidationErrorCollection {
        try {
            $action = $this->executivesLocator->getAction($type);
        } catch (InvalidArgumentException $exception) {
            return new ValidationErrorCollection($this->prefixErrors([
                'type' => new ParamValidationError('exe.validation.unknownValue', ['value' => $type]),
            ], $this->pathPrefix));
        }

        return $this->runValidations($action, $params, $normalize);
    }

    /**
     * @param string $type
     * @param array $params
     * @param bool $normalize
     *
     * @return ValidationErrorCollection
     */
    public function validateConditionParams(
        string $type,
        array &$params,
        bool $normalize = false
    ): ValidationErrorCollection {
        try {
            $action = $this->executivesLocator->getCondition($type);
        } catch (InvalidArgumentException $exception) {
            return new ValidationErrorCollection($this->prefixErrors([
                'type' => new ParamValidationError('exe.validation.unknownValue', ['value' => $type]),
            ], $this->pathPrefix));
        }

        return $this->runValidations($action, $params, $normalize);
    }

    /**
     * Processes validations on $subject
     *
     * These validations can be of {@link HasParamsSchema} or of {@link ValidatesParams} interfaces.
     *
     * First, if $subject implements Schema-based validation, this validation is run. If no errors
     * are found any remaining validation is done.
     *
     * @param Action|Condition $subject
     * @param array $params
     * @param bool $normalize
     *
     * @return ValidationErrorCollection
     */
    private function runValidations($subject, array &$params, bool $normalize): ValidationErrorCollection
    {
        $errors = [];
        if ($subject instanceof HasParamsSchema) {
            $schemaErrors = $this->processParamsBySchema($subject->getParamsSchema(), $params, $normalize);
            foreach ($schemaErrors as $field => $error) {
                $errors[$field] = $error;
            }
        }

        if (empty($errors) && $subject instanceof ValidatesParams) {
            foreach ($subject->validateParams($params) as $field => $error) {
                $errors[$field] = $error;
            }
        }

        return new ValidationErrorCollection($this->prefixErrors($errors, $this->pathPrefix));
    }

    private function processParamsBySchema(Schema $schema, array &$params, bool $normalize)
    {
        $errors = [];
        try {
            if ($schema instanceof Structure) {
                $schema = clone $schema;
                $schema->castTo('array');
            }
            $normalized = $this->getProcessor()->process($schema, $params);
            if ($normalize) {
                $params = $normalized;
            }
        } catch (ValidationException $exception) {
            foreach ($exception->getErrors() as $error) {
                $field = implode('.', $error->path);
                unset($error->path);
                $data = (array)$error + ['field' => $field];
                $errors[$field] = new ParamValidationError($error->code, $data);
            }
        }

        return $errors;
    }

    private function getProcessor(): Processor
    {
        return $this->processor ?: $this->processor = new Processor();
    }

    private function path(string $path): string
    {
        return ($this->pathPrefix ? $this->pathPrefix . '.' : '') . $path;
    }

    private function prefixErrors(array $errors, string $pathPrefix)
    {
        if ($pathPrefix) {
            $pathPrefix = rtrim($pathPrefix, '.') . '.';
        }

        $prefixedErrors = [];
        foreach ($errors as $field => $error) {
            $prefixedErrors["$pathPrefix$field"] = $error;
        }

        return $prefixedErrors;
    }
}
