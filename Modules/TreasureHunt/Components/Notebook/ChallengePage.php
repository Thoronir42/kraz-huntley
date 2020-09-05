<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Notebook;

use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\NotebookPageChallenge;
use CP\TreasureHunt\Model\Service\NotebookService;
use Nette\Application\UI;
use Nette\Localization\ITranslator;
use SeStep\NetteTypeful\Forms\PropertyControlFactory;

class ChallengePage extends UI\Control
{
    public $onAnswerSubmit = [];
    public $onFollowRevelation = [];

    /** @var NotebookPageChallenge */
    private $page;
    /** @var Challenge */
    private $challenge;
    /** @var PropertyControlFactory */
    private $controlFactory;
    /** @var ITranslator */
    private $translator;
    /** @var NotebookService */
    private $notebookService;

    public function __construct(
        NotebookPageChallenge $page,
        Challenge $challenge,
        PropertyControlFactory $controlFactory,
        ITranslator $translator,
        NotebookService $notebookService
    ) {
        $this->page = $page;
        $this->challenge = $challenge;
        $this->controlFactory = $controlFactory;
        $this->translator = $translator;
        $this->notebookService = $notebookService;
    }

    public function render()
    {
        $template = $this->template->setFile(__DIR__ . '/challengePage.latte');
        $template->challenge = $this->challenge;

        $template->now = new \DateTime();
        $template->revelations = $this->notebookService->getCLueRevelations($this->page);
        $template->inputBan = $this->notebookService->findActiveInputBan($this->page);

        $template->render();
    }

    public function createComponentKeyForm()
    {
        $form = new UI\Form();
        $form->setTranslator($this->translator);
        $form['key'] = $this->controlFactory->create('appTreasureHunt.challenge.answer', $this->challenge->keyType);
        $form->addSubmit('send', 'appTreasureHunt.tryAnswer');

        $form->onSuccess[] = function ($form, $values) {
            $answer = $values['key'];

            $this->onAnswerSubmit($this->challenge, $answer);
        };

        return $form;
    }

    public function handleFollowRevelation(int $i)
    {
        $revelations = $this->notebookService->getCLueRevelations($this->page);

        $this->onFollowRevelation($revelations[$i]);
    }

}
