<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;
use SeStep\Executives\Model\Entity\Script;

/**
 * Class Challenge
 *
 * @property string $id
 * @property string $title
 * @property string $description
 * @property string $keyType
 *
 * @property null|Script $submitScript m:hasOne(script_id)
 *
 */
class Challenge extends Entity
{
}
