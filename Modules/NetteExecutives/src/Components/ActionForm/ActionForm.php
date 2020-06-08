<?php declare(strict_types=1);

namespace SeStep\NetteExecutives\Components\ActionForm;

use Contributte\FormMultiplier\Multiplier;
use Contributte\Translation\Translator;
use SeStep\Executives\Model\ActionData;
use SeStep\Executives\Model\GenericActionData;
use SeStep\LeanExecutives\Entity\Action;
use SeStep\LeanExecutives\Entity\Condition;
use Nette\Application\UI;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\SubmitButton;
use SeStep\Executives\ModuleAggregator;

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

    public function __construct(
        ModuleAggregator $executivesModules,
        Translator $translator,
        ActionData $action = null
    ) {
        $this->executivesModules = $executivesModules;
        $this->translator = $translator;
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
        $form->addText('params', 'exe.actionParams');

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


}
