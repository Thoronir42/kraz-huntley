<?php

namespace CP\TreasureHunt\Model;


use CP\TreasureHunt\Model\Entity;
use LeanMapper\Row;
use SeStep\ModularLeanMapper\MapperModule;

class TreasureHuntMapperModule extends MapperModule
{
    public function __construct()
    {
        parent::__construct(__NAMESPACE__ . '\\Entity', __NAMESPACE__ . '\\Repository');
    }

    public function getEntityClass(string $table, Row $row = null): ?string
    {
        if (!$row) {
            return null;
        }

        if ($table == 'th__notebook_page') {
            switch ($row->type) {
                case Entity\NotebookPage::TYPE_INDEX:
                    return Entity\NotebookPageIndex::class;

                case Entity\NotebookPage::TYPE_CHALLENGE:
                    return Entity\NotebookPageChallenge::class;
            }

            return Entity\NotebookPage::class;
        }

        return null;
    }
}
