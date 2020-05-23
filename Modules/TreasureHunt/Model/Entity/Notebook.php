<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use App\Model\Entity\User;
use DateTime;
use LeanMapper\Entity;
use Nette\InvalidStateException;

/**
 * Class Notebook
 *
 * @property string $id
 * @property User $user m:hasOne(user_id)
 * @property int|null $activePage
 * @property DateTime $firstOpenedAt
 *
 * @property NotebookPage[] $pages m:belongsToMany(notebook_id)
 */
class Notebook extends Entity
{
    public function getPage(int $i, bool $need = false): ?NotebookPage
    {
        foreach ($this->pages as $page) {
            if ($page->pageNumber !== $i) {
                continue;
            }

            if ($page instanceof NotebookPageIndex) {
                $page->setPages($this->pages);
            }

            return $page;
        }

        if ($need) {
            throw new InvalidStateException("Page $i does not exist in notebook $this->id");
        }

        return null;
    }

    public function countPages(): int
    {
        return count($this->pages);
    }
}
