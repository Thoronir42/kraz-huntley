<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Notebook;

use CP\TreasureHunt\Model\Service\ChallengesService;
use CP\TreasureHunt\Model\Entity\NotebookPage;
use CP\TreasureHunt\Model\Entity\NotebookPageChallenge;
use CP\TreasureHunt\Model\Entity\NotebookPageIndex;
use Nette\Application\UI;
use Nette\InvalidArgumentException;

class IndexPage extends UI\Control
{
    /** @var NotebookPageIndex */
    private $page;
    /** @var int */
    private $activePage;
    
    private $challengesService;

    public function __construct(NotebookPageIndex $page, int $activePage, ChallengesService $challengesServ)
    {
        $this->page = $page;
        $this->activePage = $activePage;
        $this->challengesService = $challengesServ;
    }

    public function render()
    {
        // Fetch pages
        $pages = $this->filterPages($this->page->getPages());

        // Create array of [page, title] pairs
        $pairs = [];
        foreach ($pages as $page) {
            $title = "?";
            if ($page->type === NotebookPage::TYPE_CHALLENGE) {
                $challenge = $this->challengesService->getChallenge($page->getChallengeId());
                if ($challenge != null) {
                    $title = $challenge->getTitle();
                }
            }
            
            $pairs[] = [$page, $title];
        }

        // Fill template data
        $this->template->indexPairs = $pairs;

        // Render template
        $this->template->render(__DIR__ . '/indexPage.latte');
    }

    /**
     * @param NotebookPage[] $pages
     *
     * @return NotebookPage[]
     */
    private function filterPages(array $pages)
    {
        $result = [];
        foreach ($pages as $page) {
            if ($page->type !== NotebookPage::TYPE_INDEX) {
                $result[] = $page;
            }
        }

        return $result;
    }

}
