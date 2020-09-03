<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;


use DateTime;
use LeanMapper\Entity;

/**
 * @property int $id
 * @property NotebookPage $notebookPage m:hasOne(notebook_page_id)
 * @property DateTime $activeUntil
 */
class InputBan extends Entity
{

}
