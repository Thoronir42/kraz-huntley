<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;
use SeStep\LeanExecutives\Entity\Action;

/**
 * Class Challenge
 *
 * @property string $id
 * @property string $title
 * @property string $description
 * @property string $keyType
 * @property string $correctAnswer
 *
 * @property Action|null $onSubmit m:hasOne(on_submit)
 *
 */
class Challenge extends Entity
{
    protected function initDefaults()
    {
        $this->onSubmit = null;
    }
}
