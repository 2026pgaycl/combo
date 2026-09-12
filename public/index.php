<?php

declare(strict_types=1);

use App\Core\Request;
use App\Core\Router;

require __DIR__ . '/../app/bootstrap.php';

$router = new Router();
require __DIR__ . '/../app/routes.php';

$router->dispatch(Request::method(), Request::path());
