<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Notebook;

use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\NotebookPageChallenge;
use Nette\Application\UI;
use Nette\Localization\ITranslator;
use SeStep\NetteTypeful\Forms\PropertyControlFactory;

class ChallengePage extends UI\Control
{
    public $onAnswerSubmit = [];

    /** @var NotebookPageChallenge */
    private $page;
    /** @var Challenge */
    private $challenge;
    /** @var PropertyControlFactory */
    private $controlFactory;
    /** @var ITranslator */
    private $translator;

    public function __construct(
        NotebookPageChallenge $page,
        Challenge $challenge,
        PropertyControlFactory $controlFactory,
        ITranslator $translator
    ) {
        $this->page = $page;
        $this->challenge = $challenge;
        $this->controlFactory = $controlFactory;
        $this->translator = $translator;
    }

    public function render()
    {
        $template = $this->template->setFile(__DIR__ . '/challengePage.latte');
        $template->challenge = $this->challenge;

        $template->render();
    }

    public function createComponentKey()
    {
        $form = new UI\Form();
        $form->setTranslator($this->translator);
        $form['value'] = $this->controlFactory->create('value', $this->challenge->keyType);


        if ($this->page->hasActiveInputBan(new \DateTime())) {
            $form['value']->addError('appTreasureHunt.inputBanActive');
            $form['value']->setDisabled();
        } else {
            $form->addSubmit('send', 'appTreasureHunt.tryAnswer');
        }

        $form->onSuccess[] = function ($form, $values) {
            $answer = $values['value'];

            $this->onAnswerSubmit($this->challenge, $answer);
        };

        return $form;
    }

}
