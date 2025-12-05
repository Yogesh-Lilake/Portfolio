<?php
/**
 * PUBLIC INDEX.PHP
 * Front Controller (entry point of your application)
 */

require_once __DIR__ . '/../config/paths.php';
require_once CONFIG_FILE;

// Core + Routing Engine
require_once APP_FILE;
require_once CONTROLLER_FILE;

// Logger + Error Handler
require_once LOGGER_FILE;
require_once ERROR_HANDLER_FILE;

// RUN HOME CONTROLLER
$data = App::run("HomeController@index");

// RENDER HOME PAGE VIEW
require_once HOME_VIEW_FILE;
?> 