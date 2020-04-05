<?php declare(strict_types=1);

namespace SeStep\Executives\Exceptions;

use RuntimeException;

class ActionNotFoundException extends RuntimeException
{
    public function __construct(string $actionName, array $availableActions)
    {
        $strAvailableActions = implode(', ', $availableActions);
        parent::__construct("Action '$actionName' is not one of available actions ($strAvailableActions)");
    }
}
