<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;

/**
 * Class ActionCondition
 *
 * @property string $id
 * @property Action $action m:hasOne(action_id)
 * @property string $type
 * @property string $params
 */
class ActionCondition extends Entity
{
    const TYPE_KEY_MATCHES = 'key_matches';

    public static function getTypes()
    {
        return [
            self::TYPE_KEY_MATCHES => 'Odpověď je',
        ];
    }
}
