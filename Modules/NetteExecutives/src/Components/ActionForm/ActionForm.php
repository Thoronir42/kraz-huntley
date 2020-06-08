<?php declare(strict_types=1);

namespace SeStep\NetteExecutives\Components\ActionForm;

use Closure;
use Contributte\FormMultiplier\Multiplier;
use Contributte\Translation\Translator;
use Nette\UnexpectedValueException;
use SeStep\Executives\Model\ActionData;
use SeStep\Executives\Model\GenericActionData;
use SeStep\Executives\Validation\ExecutivesValidator;
use SeStep\LeanExecutives\Entity\Action;
use SeStep\LeanExecutives\Entity\Condition;
use Nette\Application\UI;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\SubmitButton;
use SeStep\Executives\ModuleAggregator;
use SeStep\NetteExecutives\Controls\ActionParamsControlFactory;
use SeStep\NetteExecutives\Controls\AssociativeArrayControl;

/**
 * Class ActionForm
 *
 * @method onSave(Form $form, ActionData $action, Condition[] $conditions)
 */
class ActionForm extends UI\Component
{
    public $onSave = [];

    /** @var ActionData */
    private $action;

    /** @var ModuleAggregator */
    private $executivesModules;
    /** @var Translator */
    private $translator;
    /** @var ActionParamsControlFactory */
    private $actionParamsControlFactory;
    /** @var ExecutivesValidator */
    private $executivesValidator;

    public function __construct(
        ModuleAggregator $executivesModules,
        Translator $translator,
        ActionParamsControlFactory $actionParamsControlFactory,
        ExecutivesValidator $executivesValidator,
        ActionData $action = null
    ) {
        $this->executivesModules = $executivesModules;
        $this->translator = $translator;
        $this->actionParamsControlFactory = $actionParamsControlFactory;
        $this->executivesValidator = $executivesValidator;

        $this->setAction($action);
    }

    public function render()
    {
        $form = $this['form'];

        $form->render();
    }

    public function setAction(?ActionData $action)
    {
        $this->action = $action;

        /** @var Form $form */
        $form = $this['form'];

        if ($action) {
            $data = [
                'type' => $action->getType(),
                'params' => $action->getParams(),
            ];

            $data['conditions'] = array_map(function ($c) {
                return [
                    'type' => $c->getType(),
                    'params' => $c->getParams(),
                ];
            }, $action->getConditions());

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

        $form->addGroup('exe.action');
        $form->addSelect('type', 'exe.actionType', $this->executivesModules->getActionsPlaceholders());
        $params = $form['params'] = $this->actionParamsControlFactory->create(null, 'exe.actionParams');

        $form->addGroup('exe.actionConditions');
        $form['conditions'] = $conditions = new Multiplier(function (Container $container) {
            $container->addSelect('type', 'exe.condition', $this->executivesModules->getConditionsPlaceholders());
            $container->addText('params', 'exe.conditionParams');
            $container->addHidden('id');
        }, 0, 5);
        $conditions->setResetKeys(false);
        $conditions->addCreateButton('exe.actionForm.addCondition');
        $conditions->addRemoveButton('exe.actionForm.removeCondition');

        $form->addGroup();
        $form->addSubmit('save');

        $form->onSubmit[] = [$this, 'normalizeParams'];

        $form->onSuccess[] = function (Form $form) {
            $values = $form->getValues();
            $conditions = (array)$values['conditions'];
            unset($values['conditions']);

            if ($this->action instanceof Action) {
                $action = $this->action;
                $action->assign($values);
            } else {
                $action = new GenericActionData($values['type'], $values['params']);
            }

            $this->onSave($form, $action, $conditions);
        };

        return $form;
    }

    public function normalizeParams(Form $form)
    {
        $values = $form->getValues('array');

        $errors = $this->executivesValidator->validateActionParams($this->action->getType(), $values['params'], true);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $errorType = $error->getErrorType();
                if ($errorType !== 'schema.validationException') {
                    // todo: localization of errors
                    throw new UnexpectedValueException("Error of type '$errorType' not recognized");
                }

                $form->addError($error->getErrorData()['message'], false);
            }
        }

        $form->setValues($values);
    }

}
