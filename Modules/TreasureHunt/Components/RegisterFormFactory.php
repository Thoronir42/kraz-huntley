<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components;

use CP\TreasureHunt\Controls\EmojiMatrixControl;
use Nette\Application\UI\Form;

class RegisterFormFactory
{
    public function create()
    {
        $form = new Form();
        $form->addText('nick', 'Jméno');
        $form['pass'] = new EmojiMatrixControl('Identifikátor');

        $form->addSubmit('register', 'Začít');
        $form->addSubmit('login', 'Vrátit se do hry');
        return $form;
    }
}
