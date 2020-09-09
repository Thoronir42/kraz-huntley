<?php declare(strict_types=1);

namespace App\Grid;


use Closure;
use Ublaboo\DataGrid\DataSource\IDataSource;
use Ublaboo\DataGrid\Utils\Sorting;

class MappingDataSource implements IDataSource
{
    /** @var IDataSource */
    private $source;
    /** @var Closure */
    private $mapElement;
    /** @var Closure */
    private $loadRelatedData;

    public function __construct(IDataSource $source, Closure $mapElement)
    {
        $this->source = $source;
        $this->mapElement = $mapElement;
    }

    /**
     * @param Closure $loadRelatedData
     */
    public function setLoadRelatedData(Closure $loadRelatedData): void
    {
        $this->loadRelatedData = $loadRelatedData;
    }


    public function getCount(): int
    {
        return $this->source->getCount();
    }

    public function getData(): array
    {
        $sourceData = $this->source->getData();
        $relatedData = $this->loadRelatedData ? call_user_func($this->loadRelatedData, $sourceData) : [];
        $data = [];
        foreach ($sourceData as $id => $row) {
            $data[$id] = call_user_func($this->mapElement, $row, $id, $relatedData);
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
