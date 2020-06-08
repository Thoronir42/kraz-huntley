<?php declare(strict_types=1);

namespace App;

use Contributte\Console;
use Nette\Application;
use Nette\Configurator;
use Nette\DI\Container;
use Tracy\Debugger;

class Bootstrap
{
    /** @var Container */
    private static $container;

    private static function getContainer()
    {
        if (self::$container) {
            return self::$container;
        }

        $rootDir = dirname(__DIR__);

        $configurator = new Configurator();
        if (getenv('DEBUG_MODE')) {
            $configurator->setDebugMode(true);
        } else {
            $debugList = [
                'whoop whoop',
                'nobody watches over me',
            ];

            $configurator->setDebugMode($debugList);
        }
        Debugger::$showLocation = true;

        $configurator->addParameters([
            'modulesDir' => __DIR__ . '/../Modules',
        ]);

        $configurator->setTempDirectory($rootDir . '/temp');
        $configurator->enableDebugger($rootDir . '/logs');

        $configurator->addConfig(__DIR__ . '/config/app.config.neon');
        $configurator->addConfig(__DIR__ . '/config/config.local.neon');

        return self::$container = $configurator->createContainer();
    }

    public static function getApplication(): Application\Application
    {
        return self::getContainer()->getByType(Application\Application::class);
    }

    public static function getCliApplication(): Console\Application
    {
        return self::getContainer()->getByType(Console\Application::class);
    }
}
