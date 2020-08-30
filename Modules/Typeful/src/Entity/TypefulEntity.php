<?php declare(strict_types=1);

namespace SeStep\Typeful\Entity;

interface TypefulEntity
{
    /**
     * @param string[] $properties names of properties
     * @return mixed[]
     */
    public function getData(array $properties);
}
