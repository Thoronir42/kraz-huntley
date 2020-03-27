<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;

/**
 * Class Action
 *
 * @property string $id
 * @property Challenge $challenge m:hasOne(challenge_id)
 * @property int $sequence
 * @property string $type
 * @property string $params
 *
 * @property Condition[] $conditions m:hasMany(action_id:th__action_has_condition:condition_id)
 */
class Action extends Entity
{
    const TYPE_ACTIVATE_CHALLENGE = 'activate_challenge';
    const TYPE_REVEAL_NARRATIVE = 'reveal_narrative';

    protected function initDefaults()
    {
        $this->sequence = 0;
    }
}
