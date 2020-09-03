<?php declare(strict_types=1);

namespace App\Latte;

use Nette\Utils\Json;

class LatteFilters {

    public static function json($value)
    {
        return Json::encode($value, Json::PRETTY);
    }
}
