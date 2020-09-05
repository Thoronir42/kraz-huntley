<?php declare(strict_types=1);

namespace CP\TreasureHunt;

use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;

class TreasureHuntRouterFactory
{
    public static function create(): Router
    {
        $router = new RouteList('TreasureHunt');
        $router
            ->addRoute('/notebook[/<page>[/]]', [
                'presenter' => 'Notebook',
                'action' => 'page',
                'page' => 1,
            ]);

        $router->add(self::getManagementRoutes());

        $router->addRoute('/intro', [
            'presenter' => 'TreasureHunt',
            'action' => 'intro',
        ]);

        $router->addRoute('/sign-out', 'TreasureHunt:signOut');

        $router->addRoute('/', [
            'presenter' => 'TreasureHunt',
            'action' => 'sign',
        ]);



        return $router;
    }

    private static function getManagementRoutes(): RouteList
    {
        $routeList = new RouteList();

        $routeList->addRoute('/manage', [
            'presenter' => 'Management',
            'action' => 'dashboard',
        ]);

        $routeList->add((new RouteList())
            ->addRoute('/manage/challenges/', [
                'presenter' => 'Challenges',
                'action' => 'index',
            ])
            ->addRoute('/manage/challenges/create', [
                'presenter' => 'Challenges',
                'action' => 'createNew',
            ])
            ->addRoute('/manage/challenges/<id>', [
                'presenter' => 'Challenges',
                'action' => 'detail',
            ])
        );

        $routeList->add((new RouteList())
            ->addRoute('/manage/narratives/', [
                'presenter' => 'Narratives',
                'action' => 'index',
            ])
            ->addRoute('/manage/narratives/create', [
                'presenter' => 'Narratives',
                'action' => 'createNew',
            ])
            ->addRoute('/manage/narratives/<narrativeId>', [
                'presenter' => 'Narratives',
                'action' => 'edit',
            ])
        );

        $routeList->add((new RouteList())
            ->addRoute('/manage/treasure-maps/', [
                'presenter' => 'TreasureMaps',
                'action' => 'index',
            ])
            ->addRoute('/manage/treasure-maps/create', [
                'presenter' => 'TreasureMaps',
                'action' => 'createNew'
            ])
            ->addRoute('/manage/treasure-maps/<id>', [
                'presenter' => 'TreasureMaps',
                'action' => 'detail'
            ])
        );

        return $routeList;
    }
}
