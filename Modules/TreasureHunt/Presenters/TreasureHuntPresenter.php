<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use App\Security\HasAppUser;
use App\Security\UserManager;
use CP\TreasureHunt\Components\RegisterFormFactory;
use CP\TreasureHunt\Model\Service\NotebookService;
use CP\TreasureHuntGallery\Model\Services\GalleryService;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;
use Nette\Security\AuthenticationException;

class TreasureHuntPresenter extends Presenter
{
    use HasAppUser;

    /** @var RegisterFormFactory @inject */
    public $registerFormFactory;

    /** @var UserManager @inject */
    public $userManager;

    /** @var NotebookService @inject */
    public $notebookService;

    /** @var GalleryService @inject */
    public $galleryService;

    /** @var ITranslator @inject */
    public $translator;

    public function renderSign()
    {
        $this->layout = 'meta';
        $this->template->appUser = $this->appUser;

        if ($this->user->isLoggedIn()) {
            $signForm = $this['signForm'];
            $signForm['nick']->controlPrototype->readonly = true;
            $signForm->setDefaults([
                'nick' => $this->appUser->nick,
            ]);

            $this->template->galleryUnlocked = $this->galleryService->hasAccess($this->appUser);
        }
    }

    public function actionSignOut()
    {
        $this->user->logout(true);
        $this->redirect('sign');
    }

    public function actionIntro()
    {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('sign');
        }
        if ($this->notebookService->getNotebookByUser($this->appUser)) {
            $this->redirect('Notebook:page');
        }

        $this->template->highlightPass = false;

        $form = $this['introForm'];
        $form->onSuccess[] = function (Form $form, $values) {
            $key = $values['key'];
            if (mb_strtolower($key) !== 'lucy') {
                $this->template->highlightPass = true;
                $form->addError('To není správné heslo...', false);

                return;
            }

            $this->notebookService->createNotebook($this->appUser);
            $this->redirect('Notebook:page');
        };
    }

    public function renderIntro()
    {
        $this->layout = 'meta';
    }

    public function createComponentSignForm()
    {
        $form = $this->registerFormFactory->create();
        $form->onSuccess[] = function (Form $form, $values) {
            $nick = $values['nick'];
            $pass = implode('-', $values['pass']);

            $submitter = $form->isSubmitted();
            if ($submitter == $form['register']) {
                if (!$this->userManager->register($nick, $pass)) {
                    $this->flashMessage("Adresát $nick si již zásilku převzal");
                }
                $this->user->login($nick, $pass);
                $this->redirect('TreasureHunt:intro');
                return;
            }

            if ($submitter == $form['login']) {
                try {
                    $this->user->login($nick, $pass);
                    $this->redirect('Notebook:page');
                } catch (AuthenticationException $exception) {
                    $form->addError("Nedaří se nám ověřit vaši identitu, zkuste to, prosíme, znovu");
                }
            }
        };

        return $form;
    }

    public function createComponentIntroForm()
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $form->addText('key', 'appTreasureHunt.challenge.password');

        $form->addSubmit('send', 'appTreasureHunt.tryAnswer');

        return $form;

    }
}
