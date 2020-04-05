<?php declare(strict_types=1);

namespace SeStep\Executives\Model\Entity;

use LeanMapper\Entity;

/**
 * Class Action
 *
 * @property string $id
 * @property Script $script m:hasOne(script_id)
 * @property int $sequence
 * @property string $type
 * @property string $params
 *
 * @property Condition[] $conditions m:hasMany(action_id:exe__action_has_condition:condition_id)
 */
class Action extends Entity
{
    protected function initDefaults()
    {
        $this->sequence = 0;
    }
}
