<?php declare(strict_types=1);

namespace App\Router;

use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;

class RouterFactory
{
    /** @var Router[] */
    private $appRouters;

    /**
     * RouterFactory constructor.
     * @param Router[] $appRouters
     */
    public function __construct(array $appRouters)
    {
        $this->appRouters = $appRouters;
    }

    public function create(): Router
    {
        $router = new RouteList();
        
        foreach ($this->appRouters as $appRouter) {
            $router[] = $appRouter;
        }

        return $router;
    }
}
