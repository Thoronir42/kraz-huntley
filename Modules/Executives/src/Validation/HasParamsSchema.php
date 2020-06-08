<?php declare(strict_types=1);

namespace SeStep\Executives\Validation;

use Nette\Schema\Schema;

interface HasParamsSchema
{
    public function getParamsSchema(): Schema;
}
