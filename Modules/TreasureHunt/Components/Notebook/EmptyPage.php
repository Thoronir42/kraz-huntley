<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Notebook;

use Nette\Application\UI;

class EmptyPage extends UI\Control
{
    public function render()
    {
        return '';
    }
}
