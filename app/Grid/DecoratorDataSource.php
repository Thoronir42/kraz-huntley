<?php declare(strict_types=1);

namespace App\Grid;


use Closure;
use Ublaboo\DataGrid\DataSource\IDataSource;
use Ublaboo\DataGrid\Utils\Sorting;

class DecoratorDataSource implements IDataSource
{
    /** @var IDataSource */
    private $source;
    /** @var Closure */
    private $decorateElement;

    public function __construct(IDataSource $source, Closure $decorateElement)
    {
        $this->source = $source;
        $this->decorateElement = $decorateElement;
    }

    public function getCount(): int
    {
        return $this->source->getCount();
    }

    public function getData(): array
    {
        $data = $this->source->getData();

        foreach ($data as $i => $item) {
            call_user_func($this->decorateElement, $item, $i);
        }

        return $data;
    }

    public function filter(array $filters): void
    {
        $this->source->filter($filters);
    }

    public function filterOne(array $condition): IDataSource
    {
        $this->source->filterOne($condition);
        return $this;
    }

    public function limit(int $offset, int $limit): IDataSource
    {
        $this->source->limit($offset, $limit);
        return $this;
    }

    public function sort(Sorting $sorting): IDataSource
    {
        $this->source->sort($sorting);
        return $this;
    }
}
