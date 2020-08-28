<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components;

use CP\TreasureHunt\Controls\EmojiMatrixControl;
use Nette\Application\UI\Form;

class RegisterFormFactory
{
    public function create()
    {
        $form = new Form();
        $form->addText('nick', 'Uživatel. jméno');
        $form['pass'] = new EmojiMatrixControl('Identifikátor (heslo)');

        $form->addSubmit('register', 'Začít (Registrovat)');
        $form->addSubmit('login', 'Vrátit se do hry (Přihlásit se)');
        return $form;
    }
}
