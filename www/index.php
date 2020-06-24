<?php

require __DIR__ . '/../vendor/autoload.php';

(new App\Bootstrap())
    ->getContainer()
    ->getByType(\Nette\Application\Application::class)
    ->run();
