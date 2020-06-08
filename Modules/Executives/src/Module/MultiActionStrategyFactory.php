<?php declare(strict_types=1);

namespace SeStep\Executives\Module;

use Nette\InvalidArgumentException;
use SeStep\Executives\Module\Actions\Strategy;

class MultiActionStrategyFactory
{
    /**
     * String key to strategy class map
     * @var string[]
     */
    private $strategies;

    public function __construct()
    {
        $this->strategies = [
            'returnOnFirstPass' => Strategy\EndOnFirstSuccess::class,
            'failOnFirstFail' => Strategy\StopOnFirstFail::class,
            'executeAll' => Strategy\ExecuteAll::class,
        ];
    }

    public function create(string $strategy): Strategy\MultiActionStrategy
    {
        $class = $this->strategies[$strategy] ?? null;
        if (!$class) {
            throw new InvalidArgumentException("Strategy '$strategy' not recognized");
        }

        return new $class();
    }

    /**
     * @return string[]
     */
    public function listStrategies(): array
    {
        return array_keys($this->strategies);
    }
}
