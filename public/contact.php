<?php
require_once dirname(__DIR__) . '/config/paths.php';
require_once CONFIG_PATH . 'config.php';

// Core + Routing Engine
require_once CORE_PATH . 'App.php';
require_once CORE_PATH . 'Controller.php';

// Logger + Error Handler
require_once LOGGER_FILE;
require_once CORE_PATH . 'ErrorHandler.php';

$data = App::run("ContactController@index");

require_once CONTACT_VIEW_FILE  // app/views/pages/about.php
?>