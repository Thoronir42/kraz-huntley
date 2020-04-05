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
            ->addRoute('/notebook[/]', [
                'presenter' => 'Notebook',
                'action' => 'index',
            ])
            ->addRoute('/notebook/<page>', [
                'presenter' => 'Notebook',
                'action' => 'page',
            ])
            ->addRoute('/narratives/<id>', [
                'presenter' => 'Narratives',
                'action' => 'view',
            ]);

        $router->add(self::getManagementRoutes());

        $router->addRoute('/', [
            'presenter' => 'TreasureHunt',
            'action' => 'intro',
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
            ->addRoute('/manage/challenges/<challengeId>/addAction', [
                'presenter' => 'ChallengeAction',
                'action' => 'add',
            ])
            ->addRoute('/manage/challenges/<challengeId>/actions/<actionId>', [
                'presenter' => 'ChallengeAction',
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

        return $routeList;
    }
}
