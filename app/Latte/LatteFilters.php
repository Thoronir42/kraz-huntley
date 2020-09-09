<?php declare(strict_types=1);

namespace App\Latte;

use DateTime;
use Nette\Utils\Json;

class LatteFilters
{
    const IN_TIME_FORMATS = [
        'h' => ['hodinu', 'hodiny', 'hodin'],
        'i' => ['minutu', 'minuty', 'minut'],
        's' => ['vteřinu', 'vteřiny', 'vteřin'],
    ];

    public static function json($value)
    {
        return Json::encode($value, Json::PRETTY);
    }

    public static function remainingTimeCzech(?DateTime $dateTime): string
    {
        if (!$dateTime) {
            return 'nyní';
        }

        $diff = (new DateTime())->diff($dateTime);
        $totalSeconds = $diff->h * 3600 + $diff->i * 60 + $diff->s;
        if ($diff->invert || !$totalSeconds) {
            return 'nyní';
        }

        $result = '';
        foreach (self::IN_TIME_FORMATS as $key => $unitFormats) {
            if (!($unitValue = self::formatTimeUnit($diff->$key, $unitFormats))) {
                continue;
            }

            if ($result) {
                $result .= ', ';
            }
            $result .= $unitValue;
        }

        return 'za ' . $result;
    }

    private static function formatTimeUnit(int $count, array $unitForms): string
    {
        if ($count < 1) {
            return '';
        }
        if ($count === 1) {
            return $unitForms[0];
        }
        if ($count < 5) {
            return "$count $unitForms[1]";
        }

        return "$count $unitForms[2]";
    }
}
