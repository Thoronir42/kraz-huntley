<?php declare(strict_types=1);

namespace SeStep\NetteExecutives\Latte;

use SeStep\Executives\ExecutivesLocalization;

class ExecutivesLatteFilters
{
    /** @var ExecutivesLocalization */
    private $localization;

    public function __construct(ExecutivesLocalization $localization)
    {
        $this->localization = $localization;
    }

    public function actionPlaceholder(string $actionType): string
    {
        return $this->localization->getActionPlaceholder($actionType);
    }

    public function conditionPlaceholder(string $conditionType): string
    {
        return $this->localization->getConditionPlaceholder($conditionType);
    }
}
