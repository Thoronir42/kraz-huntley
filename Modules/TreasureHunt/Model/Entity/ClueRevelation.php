<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;


use DateTime;
use LeanMapper\Entity;

/**
 * @property string $id
 * @property NotebookPage $notebookPage m:hasOne(notebook_page_id)
 * @property DateTime|null $expiresOn
 *
 * @property string $clueType
 * @property array $clueArgs
 *
 * @property DateTime $dateCreated
 */
class ClueRevelation extends Entity
{
    private $clueArgsArr;

    /**
     * @return array
     */
    public function getClueArgs(): array
    {
        if (!$this->clueArgsArr) {
            $this->clueArgsArr = json_decode($this->row->clue_args, true);
        }

        return $this->clueArgsArr;
    }

    /**
     * @param array $clueArgs
     *
     * @return self
     */
    public function setClueArgs(array $clueArgs): self
    {
        $this->clueArgsArr = $clueArgs;
        $this->row->clue_args = json_encode($clueArgs);

        return $this;
    }

}
