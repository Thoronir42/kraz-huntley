<?php declare(strict_types=1);

namespace App\LeanMapper\Exceptions;

use Dibi\Exception;

class TransactionFailedException extends Exception
{
    public function __construct(string $message, \Exception $exception)
    {
        $sql = $exception instanceof Exception ? $exception->getSql() : null;
        parent::__construct($message, $exception->getCode(), $sql, $exception);
    }
}
