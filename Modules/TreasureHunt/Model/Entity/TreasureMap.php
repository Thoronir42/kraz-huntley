<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;

class TreasureMap
{
    /** @var int */
    public $width;
    /** @var int */
    public $height;

    /** @var int */
    public $tilingX;
    /** @var int */
    public $tilingY;

    /** @var string[] */
    public $files;
    
}
