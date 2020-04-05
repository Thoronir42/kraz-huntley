<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;

/**
 * @property string $id
 * @property string $title
 * @property string $content
 * @property Condition|null $condition m:hasOne(condition_id)
 */
class Narrative extends Entity
{

}
