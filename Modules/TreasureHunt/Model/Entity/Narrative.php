<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;
use SeStep\LeanExecutives\Entity\Condition;

/**
 * @property string $id
 * @property string $title
 * @property string $content
 *
 * @property Challenge|null $followingChallenge m:hasOne(following_challenge_id)
 */
class Narrative extends Entity
{

}
