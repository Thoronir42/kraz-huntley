<?php declare(strict_types=1);

namespace CP\TreasureHuntGallery\Model\Repository;

use App\LeanMapper\Repository;
use CP\TreasureHunt\Model\Entity\Notebook;

class ChallengeViewRepository extends Repository
{
    public function unlockChallenges(Notebook $notebook, array $challengeIds)
    {
        [$notebookColumn, $challengeColumn] = $this->columnsByProperties('notebook', 'challenge');

        $fluent = $this->connection->select($challengeColumn)
            ->from($this->getTable());

        $this->filter->apply($fluent, [
            'notebook' => $notebook,
            'challenge' => $challengeIds
        ], $this->getEntityClass());
        $unlockedChallenges = $fluent->fetchPairs();

        $challengesToBeUnlocked = array_diff($challengeIds, $unlockedChallenges);

        $result = [];
        foreach ($challengesToBeUnlocked as $challengeId) {
            $row = [
                $notebookColumn => $notebook->id,
                $challengeColumn => $challengeId,
            ];
            $result[] = $this->connection->insert($this->getTable(), $row)
                ->execute('n');
        }

    }

    public function getUnlockedChallenges(Notebook $notebook)
    {
        [$challengeColumn] = $this->columnsByProperties('challenge');
        $fluent = $this->connection->select($challengeColumn)->from($this->table);
        $this->filter->apply($fluent, [
            'notebook' => $notebook,
        ], $this->getEntityClass());

        return $fluent->fetchPairs();
    }
}
