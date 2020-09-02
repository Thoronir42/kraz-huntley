<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Challenge;


use Contributte\Translation\Translator;
use Nette\InvalidArgumentException;
use SeStep\Executives\Model\ActionData;
use SeStep\Executives\Model\GenericActionData;
use SeStep\Executives\Module\Actions\MultiAction;
use SeStep\Executives\Module\MultiActionStrategyFactory;
use SeStep\Executives\Validation\ExecutivesValidator;
use SeStep\LeanExecutives\Entity\Action;
use SeStep\LeanExecutives\Entity\Condition;
use Nette\Application\UI;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use SeStep\Executives\ModuleAggregator;
use SeStep\NetteExecutives\Controls\AssociativeArrayControl;

/**
 * Class ActionForm
 *
 * @method onSave(Form $form, ActionData $action, Condition[] $conditions)
 */
class OnSubmitActionsForm extends UI\Control
{
    public $onSave = [];

    /** @var ActionData */
    private $action;

    /** @var ModuleAggregator */
    private $executivesModules;
    /** @var Translator */
    private $translator;
    /** @var ExecutivesValidator */
    private $executivesValidator;
    /** @var MultiActionStrategyFactory */
    private $multiActionStrategyFactory;

    public function __construct(
        ModuleAggregator $executivesModules,
        Translator $translator,
        ExecutivesValidator $executivesValidator,
        MultiActionStrategyFactory $multiActionStrategyFactory,
        ActionData $action = null
    ) {
        $this->executivesModules = $executivesModules;
        $this->translator = $translator;
        $this->executivesValidator = $executivesValidator;
        $this->multiActionStrategyFactory = $multiActionStrategyFactory;

        $this->setAction($action);
    }

    public function render()
    {
        $this['form']['params']->controlPrototype->class[] = 'form-control';

        $this->template->setFile(__DIR__ . '/onSubmitActionsForm.latte');
        $this->template->actions = $this->executivesModules->getActionsPlaceholders();
        $this->template->conditions = $this->executivesModules->getConditionsPlaceholders();
        $this->template->multiActionStrategies = $this->multiActionStrategyFactory->listStrategies();

        $this->template->render();
    }

    public function setAction(?ActionData $action)
    {
        $multiActionType = $this->executivesModules->getActionTypeByClass(MultiAction::class);

        if (!$action) {
            $action = new GenericActionData($multiActionType, [
                'strategy' => 'returnOnFirstPass',
                'actions' => [],
            ]);
        }

        $this->action = $action;

        /** @var Form $form */
        $form = $this['form'];

        if ($action) {
            if ($action->getType() !== 'exe.multiAction') {
                throw new InvalidArgumentException("Only multiaction actions are supported");
            }

            $data = [
                'params' => $action->getParams(),
            ];

            $form->setDefaults($data);
        }

        /** @var SubmitButton $save */
        $save = $form['save'];
        $save->setCaption('exe.actionForm.submit' . ($action ? 'Update' : 'Create'));
    }

    public function createComponentForm()
    {
        $form = new Form();
        $form->setTranslator($this->translator);

        $params = $form['params'] = new AssociativeArrayControl('appTreasureHunt.challenge.onSubmitActionsList');

        $form->addSubmit('save', 'messages.set');

        $form->onValidate[] = [$this, 'normalizeParams'];

        $form->onSuccess[] = function (Form $form) {
            $values = $form->getValues();

            if ($this->action instanceof Action) {
                $action = $this->action;
                $action->assign($values);
            } else {
                $action = new GenericActionData($values['type'], $values['params']);
            }

            $this->onSave($form, $action, []);
        };

        return $form;
    }

    public function normalizeParams(Form $form)
    {
        $values = $form->getValues('array');

        $errors = $this->executivesValidator->validateActionParams($this->action->getType(), $values['params'], true);
        if (!empty($errors)) {
            $trimStart = mb_strlen('params.');
            foreach ($errors as $field => $error) {
                $errorType = $error->getErrorType();
                $errorData = $error->getErrorData();

                if ($errorType === 'schema.validationException') {
                    $form->addError($errorData['message'], false);
                    continue;
                }

                // since the form displays just params of a multiAction, all paths start by 'params.' - trim it
                $field = mb_substr($field, $trimStart);

                $form->addError("$field: " . $this->translator->translate($error->getErrorType(), $errorData), false);
            }
        }

        $form->setValues($values);
    }

}
