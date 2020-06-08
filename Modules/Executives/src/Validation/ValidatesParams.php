<?php declare(strict_types=1);

namespace SeStep\Executives\Validation;

interface ValidatesParams
{
    /**
     * @param array $params
     * @return ParamValidationError[]
     */
    public function validateParams(array $params): array;
}
