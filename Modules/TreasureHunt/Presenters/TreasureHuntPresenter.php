<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use App\Security\UserManager;
use CP\TreasureHunt\Components\RegisterFormFactory;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;

class TreasureHuntPresenter extends Presenter
{
    /** @var RegisterFormFactory @inject */
    public $registerFormFactory;

    /** @var UserManager @inject */
    public $userManager;

    public function actionIntro()
    {
        /** @var Form $form */
        $form = $this['introForm'];
    }

    public function renderIntro()
    {
        $this->layout = 'meta';
    }

    public function createComponentIntroForm()
    {
        $form = $this->registerFormFactory->create();
        $form->onSuccess[] = function (Form $form, $values) {
            $nick = $values['nick'];
            $pass = implode('-', $values['pass']);

            $submitter = $form->isSubmitted();
            if ($submitter == $form['register']) {
                $result = $this->userManager->register($nick, $pass);
                if (!$result) {
                    $this->flashMessage("Adresát $nick si již zásilku převzal (Uživatel již existuje)");
                }
            }

            if ($result ?? false || $submitter == $form['login']) {
                try {
                    $this->user->login($nick, $pass);
                    $this->redirect('Notebook:page');
                } catch (AuthenticationException $exception) {
                    $this->flashMessage("Nedaří se nám ověřit vaši identitu, zkuste to, prosíme, znovu");
                }
            }
        };

        return $form;
    }
}
