<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

use LeanMapper\Entity;

/**
 * Class NotebookPage
 *
 * @property string $id
 * @property Notebook $notebook m:hasOne(notebook_id)
 * @property int $pageNumber
 * @property string $type m:enum(self::TYPE_*)
 * @property array $params
 */
class NotebookPage extends Entity
{
    const TYPE_INDEX = 'index';
    const TYPE_CHALLENGE = 'challenge';

    private $paramsArray;

    /** @return array */
    public function getParams(): array
    {
        if (!is_array($this->paramsArray)) {
            $this->paramsArray = json_decode($this->row->params, true);
        }

        return $this->paramsArray;
    }

    /**
     * @param array $params
     * @return NotebookPage
     */
    public function setParams(array $params): NotebookPage
    {
        $this->paramsArray = $params;
        $this->row->params = json_encode($params);

        return $this;
    }


}
