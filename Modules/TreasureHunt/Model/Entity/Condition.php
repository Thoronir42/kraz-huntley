<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;

/**
 * @property string $id
 * @property string $type
 * @property string $params
 */
class Condition extends Entity
{
    const TYPE_KEY_MATCHES = 'key_matches';

    public static function getTypes()
    {
        return [
            self::TYPE_KEY_MATCHES => 'Odpověď je',
        ];
    }
}
