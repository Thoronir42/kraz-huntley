<?php declare(strict_types=1);

namespace SeStep\Executives;

use Nette\Caching\Cache;
use Nette\Caching\Storages\MemoryStorage;
use Nette\InvalidArgumentException;

/**
 * Aggregates Actions and Conditions of given modules
 *
 * Retrieves Action and Condition and lists of their FQNs or I18N placeholders
 */
class ModuleAggregator
{
    /** @var ExecutivesModule[] */
    private $modules;
    /** @var ExecutivesLocalization */
    private $localization;
    /** @var Cache */
    private $cache;

    public function __construct(
        array $modules,
        ExecutivesLocalization $localization,
        Cache $cache = null
    ) {
        $this->modules = $modules;
        $this->localization = $localization;
        $this->cache = $cache ?: new Cache(new MemoryStorage());
    }

    /**
     * @param string $type action name
     * @return string action FQN
     */
    public function getAction(string $type): string
    {
        $action = $this->getActions()[$type] ?? null;
        if (!$action) {
            throw new InvalidArgumentException("Action type '$type' is not recognized");
        }

        return $action;
    }

    /**
     * @param string $type condition name
     * @return string condition FQN
     */
    public function getCondition(string $type): string
    {
        $condition = $this->getConditions()[$type] ?? null;
        if (!$condition) {
            throw new InvalidArgumentException("Condition type '$type' is not recognized");
        }

        return $condition;
    }

    /**
     * @return string[] associative array of action FQNs
     */
    public function getActions(): array
    {
        return $this->cache->load('actions', function () {
            $actions = [];
            foreach ($this->modules as $moduleName => $module) {
                foreach ($module->getActions() as $type => $class) {
                    $action = "$moduleName.$type";
                    $actions[$action] = $class;
                }
            }

            return $actions;
        });
    }

    /**
     * @return string[] associative array of conditions FQNs
     */
    public function getConditions(): array
    {
        return $this->cache->load('conditions', function () {
            $conditions = [];
            foreach ($this->modules as $moduleName => $module) {
                foreach ($module->getConditions() as $type => $class) {
                    $condition = "$moduleName.$type";
                    $conditions[$condition] = $class;
                }
            }

            return $conditions;
        });
    }

    /**
     * @return string[] associative array of action i18n placeholders
     */
    public function getActionsPlaceholders(): array
    {
        return $this->cache->load('i18n.actions', function () {
            $types = [];
            foreach ($this->modules as $moduleName => $module) {
                $localizationPrefix = $module->getLocalizationName() ?: $moduleName;
                foreach (array_keys($module->getActions()) as $type) {
                    $action = "$moduleName.$type";
                    $types[$action] = $this->localization->getActionPlaceholder("$localizationPrefix.$type");
                }
            }

            return $types;
        });
    }

    /**
     * @return string[] associative array of condition i18n placeholders
     */
    public function getConditionsPlaceholders(): array
    {
        return $this->cache->load('i18n.conditions', function () {
            $types = [];
            foreach ($this->modules as $moduleName => $module) {
                $localizationPrefix = $module->getLocalizationName() ?: $moduleName;
                foreach (array_keys($module->getConditions()) as $type) {
                    $condition = "$moduleName.$type";
                    $types[$condition] = $this->localization->getConditionPlaceholder("$localizationPrefix.$type");
                }
            }

            return $types;
        });
    }


}
