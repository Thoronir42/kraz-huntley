<?php declare(strict_types=1);

namespace App;

use Contributte\Console;
use CP\TreasureHunt\Model\Service\TreasureHuntService;
use Nette\Application;
use Nette\Configurator;
use Nette\DI\Container;
use Tracy\Debugger;

class Bootstrap
{
    /** @var string[] */
    private $additionalConfigFiles;

    /** @var Container */
    private $container;

    public function __construct(string ...$additionalConfigFiles)
    {
        $this->additionalConfigFiles = $additionalConfigFiles;
    }

    public function getContainer()
    {
        if ($this->container) {
            return $this->container;
        }

        $rootDir = dirname(__DIR__);

        $configurator = new Configurator();
        if (getenv('DEBUG_MODE')) {
            $configurator->setDebugMode(true);
        } else {
            $file = dirname(__DIR__) . '/config/debug_ips.txt';
            $debugList = file_exists($file) ? explode("\n", file_get_contents($file)) : false;

            $configurator->setDebugMode($debugList);
        }
        Debugger::$showLocation = true;

        $configurator->addParameters([
            'rootDir' => $rootDir,
            'modulesDir' => __DIR__ . '/../Modules',
        ]);

        $configurator->setTempDirectory($rootDir . '/temp');
        $configurator->enableDebugger($rootDir . '/logs');

        $configurator->addConfig($rootDir . '/config/app.config.neon');
        $configurator->addConfig($rootDir . '/config/config.local.neon');
        foreach ($this->additionalConfigFiles as $configFile) {
            $configurator->addConfig($rootDir . '/config/' . $configFile);
        }

        return $this->container = $configurator->createContainer();
    }

}
