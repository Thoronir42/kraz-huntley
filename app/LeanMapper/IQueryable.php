<?php declare(strict_types=1);

namespace App\LeanMapper;

use Dibi\Fluent;

interface IQueryable
{
    public function makeEntity($row);

    public function makeEntities(array $rows): array;
}
