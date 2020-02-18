<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Repository;

use App\LeanMapper\Repository;
use App\Model\Entity\User;
use CP\TreasureHunt\Model\Entity\Notebook;

class NotebookRepository extends Repository
{
    public function create(User $user, int $activePage = null): Notebook
    {
        $notebook = new Notebook();
        $notebook->user = $user;
        $notebook->firstOpenedAt = new \DateTime();
        $notebook->activePage = $activePage;

        $this->persist($notebook);

        return $notebook;
    }
}
