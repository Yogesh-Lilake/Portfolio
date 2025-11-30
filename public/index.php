<?php
/**
 * PUBLIC INDEX.PHP
 * Front Controller (entry point of your application)
 */

require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_PATH . 'config.php';

// Core + Routing Engine
require_once CORE_PATH . 'App.php';
require_once CORE_PATH . 'Controller.php';

// Logger + Error Handler
require_once LOGGER_PATH;
require_once CORE_PATH . 'ErrorHandler.php';

// RUN HOME CONTROLLER
$data = App::run("HomeController@index");

// RENDER HOME PAGE VIEW
require_once HOME_PATH . 'index.php';
?>