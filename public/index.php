<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/paths.php';

// Bootstrap (autoload helpers, models, controllers, env, etc.)
require __DIR__ . '/bootstrap.php';

// Load Router
require __DIR__ . '/../app/core/Router.php';

$router = new Router();

// Load all routes
require __DIR__ . '/../routes/web.php';

// Dispatch the current request
$router->dispatch();
