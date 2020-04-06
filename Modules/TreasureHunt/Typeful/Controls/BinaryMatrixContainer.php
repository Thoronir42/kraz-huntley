<?php declare(strict_types=1);

namespace CP\TreasureHunt\Typeful\Controls;

use Nette\Forms\Container;
use Nette\Forms\Controls\Checkbox;
use Nette\NotImplementedException;

class BinaryMatrixContainer extends Container
{

    /**
     * @param bool[][] $rows
     */
    public function __construct(array $rows)
    {
        foreach ($rows as $row) {
            $this[] = $this->createRowContainer($row);
        }
    }

    private function createRowContainer(array $row): Container
    {
        $container = new Container();

        for ($i = 0; $i < count($row); $i++) {
            $container[] = new Checkbox();
        }

        return $container;
    }

    /**
     * @param bool|bool[][] $disabled
     */
    public function setDisabled($disabled = true)
    {
        throw new NotImplementedException();
    }
}
