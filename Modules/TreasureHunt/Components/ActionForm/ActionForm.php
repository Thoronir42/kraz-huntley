<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\ActionForm;

use Contributte\FormMultiplier\Multiplier;
use CP\TreasureHunt\Model\Entity\Action;
use CP\TreasureHunt\Model\Entity\ActionCondition;
use Nette\Application\UI;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;

class ActionForm extends UI\Component
{
    /** @var Action */
    private $action;

    public $onSave = [];

    public function __construct(Action $action = null)
    {
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
            $data['conditions'] = array_map(function($c) {return $c->getData(); }, $action->conditions);
            $form->setDefaults($data);
        }

        /** @var SubmitButton $save */
        $save = $form['save'];
        $save->setCaption($action ? 'Upravit' : 'Vytvořit');

    }

    public function createComponentForm()
    {
        $form = new Form();
        $form->addGroup('Akce');
        $form->addSelect('type', 'Typ akce', [
            Action::TYPE_ACTIVATE_CHALLENGE => 'Aktivovat výzvu',
            Action::TYPE_REVEAL_NARRATIVE => 'Zobrazit průpravu',
        ]);
        $form->addText('params', 'Parametry akce');

        $form->addGroup('Podmínky');
        $form['conditions'] = $conditions = new Multiplier(function (Container $container) {
            $container->addSelect('type', 'Podmínka', ActionCondition::getTypes());
            $container->addText('params', 'Parametry podmínky');
            $container->addHidden('id');
        }, 1, 5);
        $conditions->setResetKeys(false);
        $conditions->addCreateButton('Přidat podmínku');
        $conditions->addRemoveButton('Odebrat podmínku');

        $form->addGroup();
        $form->addSubmit('save');

        $form->onSuccess[] = function ($form) {
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
