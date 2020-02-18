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
 */
class Action extends Entity
{
    const TYPE_REVEAL_CHALLENGE = 'reveal_challenge';
    const TYPE_RETURN_TO_CHALLENGE = 'return_to_challenge';
    const TYPE_REVEAL_NARRATIVE = 'reveal_narrative';
}
