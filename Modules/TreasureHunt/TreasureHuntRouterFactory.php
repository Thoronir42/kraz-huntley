<?php declare(strict_types=1);

namespace CP\TreasureHunt;


use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;

class TreasureHuntRouterFactory
{
    public static function create(): Router
    {
        $router = new RouteList('TreasureHunt');
        $router[] = new Route('/notebook/', [
            'presenter' => 'Notebook',
            'action' => 'index',
        ]);
        $router[] = new Route('/notebook/<page>', [
            'presenter' => 'Notebook',
            'action' => 'page',
        ]);

        $router[] = new Route('/', [
            'presenter' => 'TreasureHunt',
            'action' => 'intro',
        ]);

        return $router;
    }
}
