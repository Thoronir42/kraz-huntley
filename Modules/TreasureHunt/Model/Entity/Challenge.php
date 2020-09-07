<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;
use SeStep\LeanExecutives\Entity\Action;

/**
 * Class Challenge
 *
 * @property string $id
 * @property string|null $code
 * @property string $title
 * @property string $description
 * @property string $keyType
 * @property array $keyTypeOptions
 * @property string $correctAnswer
 *
 * @property Action|null $onSubmit m:hasOne(on_submit)
 *
 */
class Challenge extends Entity
{
    private $keyTypeOptionsArray;

    protected function initDefaults()
    {
        $this->onSubmit = null;
    }

    public function getKeyTypeOptions(): array
    {
        if (!$this->keyTypeOptionsArray) {
            if(!$this->row->key_type_options) {
                $this->keyTypeOptionsArray = [];
            } else {
                $this->keyTypeOptionsArray = json_decode($this->row->key_type_options, true);
            }
        }

        return $this->keyTypeOptionsArray;
    }

    public function setKeyTypeOptions(array $keyTypeOptions): self
    {
        $this->keyTypeOptionsArray = $keyTypeOptions;
        $this->row->key_type_options = json_encode($keyTypeOptions);

        return $this;
    }

}
