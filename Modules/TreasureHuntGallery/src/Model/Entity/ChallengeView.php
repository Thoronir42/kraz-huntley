<?php declare(strict_types=1);

namespace CP\TreasureHuntGallery\Model\Entity;


use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\Notebook;
use LeanMapper\Entity;

/**
 * @property int $id
 * @property Notebook $notebook m:hasOne(notebook_id)
 * @property Challenge $challenge m:hasOne(challenge_id)
 */
class ChallengeView extends Entity
{

}
