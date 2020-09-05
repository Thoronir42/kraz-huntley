<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use CP\TreasureHunt\Model\Entity\Attributes\TreasureMapFileAttributes;
use LeanMapper\Entity;

/**
 * @property string $id
 * @property string $name
 * @property string $filename
 *
 * @property int $tilingX
 * @property int $tilingY
 */
class TreasureMap extends Entity
{
    /** @var TreasureMapFileAttributes|null */
    public $fileAttributes;
    
}
