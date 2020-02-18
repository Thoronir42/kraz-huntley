<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;

/**
 * Class NotebookPage
 *
 * @property string $id
 * @property Notebook $notebook m:hasOne(notebook_id)
 * @property int $pageNumber
 * @property string $type m:enum(self::TYPE_*)
 */
class NotebookPage extends Entity
{
    const TYPE_INDEX = 'index';
    const TYPE_CHALLENGE = 'challenge';
}
