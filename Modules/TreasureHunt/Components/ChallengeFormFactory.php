<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components;

use CP\TreasureHunt\Model\Entity\Challenge;
use Nette\Application\UI\Form;

class ChallengeFormFactory
{
    public function create()
    {
        $form = new Form();

        $form->addText('title', 'Název');
        $form->addTextArea('description', 'Výzva');
        $keyType = $form->addRadioList('keyType', 'Typ klíče', [
            Challenge::TYPE_TEXT => 'Text',
            Challenge::TYPE_NUMBER => 'Číslo',
        ]);
        $keyType->setDefaultValue(Challenge::TYPE_TEXT);


        $form->addSubmit('save');

        return $form;
    }
}
