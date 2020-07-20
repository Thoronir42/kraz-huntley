<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Challenge;

use Nette\Application\UI\ITemplateFactory;
use Nette\Forms\Form;
use Nette\Forms\IFormRenderer;

class ChallengeFormNotebookRenderer implements IFormRenderer
{
    /** @var ITemplateFactory */
    private $templateFactory;

    public function __construct(ITemplateFactory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }


    function render(Form $form): string
    {
        $template = $this->templateFactory->createTemplate();
        $template->form = $form;

        $template->setFile(__DIR__ . '/challengeFormNotebook.latte');

        return (string)$template;
    }
}
