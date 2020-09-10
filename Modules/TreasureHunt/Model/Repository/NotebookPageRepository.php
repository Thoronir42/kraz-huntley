<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Repository;

use App\LeanMapper\Repository;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\Notebook;
use CP\TreasureHunt\Model\Entity\NotebookPage;
use DateTime;
use Dibi\Expression;

class NotebookPageRepository extends Repository
{
    public function createIndex(Notebook $notebook): NotebookPage
    {
        return $this->createPage($notebook, NotebookPage::TYPE_INDEX);
    }

    public function createChallenge(Notebook $notebook, Challenge $challenge)
    {
        return $this->createPage($notebook, NotebookPage::TYPE_CHALLENGE, [
            'challengeId' => $challenge->id,
        ]);
    }

    private function createPage(Notebook $notebook, string $type, array $params = []): NotebookPage {
        $page = new NotebookPage();
        $page->notebook = $notebook;
        $page->pageNumber = $this->getNextInSequence('pageNumber', ['notebook' => $notebook], NotebookPage::class);
        $page->type = $type;
        $page->params = $params;
        $page->discoveredOn = new DateTime();

        $this->persist($page);

        return $page;
    }

    /**
     * @param Notebook[] $notebooks
     */
    public function getActivePages(array $notebooks)
    {
        [$notebookColumn, $pageNumberColumn] = $this->columnsByProperties('notebook', 'pageNumber');
        $expressions = [];
        foreach ($notebooks as $notebook) {
            $expressions[] = new Expression("%n = ? AND %n = ?", $notebookColumn, $notebook->id, $pageNumberColumn, $notebook->activePage);
        }

        $pageRows = $this->select()
            ->where('%or', $expressions)
            ->fetchAssoc($notebookColumn);

        $pages = [];
        foreach ($pageRows as $key => $row) {
            $pages[$key] = $this->makeEntity($row);
        }

        return $pages;
    }
}
