<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Notebook;

use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\NotebookPageChallenge;
use Nette\Application\UI;
use SeStep\Typeful\Forms\PropertyControlFactory;

class ChallengePage extends UI\Control
{
    public $onAnswerSubmit = [];

    /** @var NotebookPageChallenge */
    private $page;
    /** @var Challenge */
    private $challenge;
    /** @var PropertyControlFactory */
    private $controlFactory;

    public function __construct(
        NotebookPageChallenge $page,
        Challenge $challenge,
        PropertyControlFactory $controlFactory
    ) {
        $this->page = $page;
        $this->challenge = $challenge;
        $this->controlFactory = $controlFactory;
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
        $form['value'] = $this->controlFactory->create('value', $this->challenge->keyType);

        $form->addSubmit('send');

        $form->onSuccess[] = function($form, $values) {
            $answer = $values['value'];

            $this->onAnswerSubmit($this->challenge, $answer);
        };

        return $form;
    }

}
