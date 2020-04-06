<?php declare(strict_types=1);

namespace SeStep\Executives\Components\ActionForm;

use Contributte\FormMultiplier\Multiplier;
use Contributte\Translation\Translator;
use SeStep\Executives\Model\Entity\Action;
use SeStep\Executives\Model\Entity\Condition;
use Nette\Application\UI;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\SubmitButton;
use SeStep\Executives\Model\Service\ActionsService;

/**
 * Class ActionForm
 *
 * @method onSave(Form $form, Action $action, Condition[] $conditions)
 */
class ActionForm extends UI\Component
{
    public $onSave = [];

    /** @var Action */
    private $action;

    /** @var ActionsService */
    private $actionsService;
    /** @var Translator */
    private $translator;

    public function __construct(ActionsService $actionsService, Translator $translator, Action $action = null)
    {
        $this->actionsService = $actionsService;
        $this->translator = $translator;
        $this->setAction($action);
    }

    public function render()
    {
        $form = $this['form'];

        return $form->render();
    }

    public function setAction(?Action $action)
    {
        $this->action = $action ?: new Action();

        /** @var Form $form */
        $form = $this['form'];

        if ($action) {
            $data = $action->getData();
            $data['conditions'] = array_map(function ($c) {
                return $c->getData();
            }, $action->conditions);
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
        $form->addSelect('type', 'exe.actionType', $this->actionsService->getActionTypes());
        $form->addText('params', 'exe.actionParams');

        $form->addGroup('exe.actionConditions');
        $form['conditions'] = $conditions = new Multiplier(function (Container $container) {
            $container->addSelect('type', 'exe.condition', $this->actionsService->getConditionTypes());
            $container->addText('params', 'exe.conditionParams');
            $container->addHidden('id');
        }, 1, 5);
        $conditions->setResetKeys(false);
        $conditions->addCreateButton('exe.actionForm.addCondition');
        $conditions->addRemoveButton('exe.actionForm.removeCondition');

        $form->addGroup();
        $form->addSubmit('save');

        $form->onSuccess[] = function (Form $form) {
            $values = $form->getValues();
            $conditions = (array)$values['conditions'];
            unset($values['conditions']);

            $action = $this->action;
            $action->assign($values);


            $this->onSave($form, $action, $conditions);
        };

        return $form;
    }


}
