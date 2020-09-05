<?php declare(strict_types=1);

namespace SeStep\Executives\Module;

use DateTime;

// todo: allow injection of reference point in time
trait RelativeDate
{
    protected function getDateFrom($string): ?DateTime
    {
        try {
            return new DateTime($string);
        } catch (\Throwable $ex) {
            return null;
        }
    }
}
