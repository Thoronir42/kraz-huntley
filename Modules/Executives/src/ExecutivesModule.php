<?php declare(strict_types=1);

namespace SeStep\Executives;

/**
 * Interface mediating modular extensibility
 */
interface ExecutivesModule
{

    /**
     * Localization name overrides module name in placeholders
     *
     * @return string
     */
    public function getLocalizationName(): string;
    
    /**
     * Returns associative array of Action classes
     *
     * @return string[]
     */
    public function getActions(): array;

    /**
     * Returns associative array of Condition classes
     *
     * @return array
     */
    public function getConditions(): array;
}
