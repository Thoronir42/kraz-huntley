<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;

/**
 * Class Challenge
 *
 * @property string $id
 * @property string $title
 * @property string $description
 * @property string $keyType m:enum(Challenge::TYPE_*)
 *
 * @property Action[] $actions m:belongsToMany(challenge_id)
 *
 */
class Challenge extends Entity
{
    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';

}
