<?php declare(strict_types=1);

namespace CP\TreasureHuntGallery;

use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;

class TreasureHuntGalleryRouterFactory
{
    public static function create(): Router
    {
        return (new RouteList('TreasureHuntGallery'))
            ->addRoute('/gallery', 'Gallery:index')
            ->addRoute('/gallery/challenge/<id>', 'Gallery:challenge');
    }
}
