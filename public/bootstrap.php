<?php

require __DIR__ . '/../vendor/autoload.php';

foreach (glob(__DIR__ . '/../app/Helpers/*.php') as $file) {
    require $file;
}

spl_autoload_register(function ($class) {

    $prefix = "app\\";
    if (strpos($class, $prefix) === 0) {
        $relative = substr($class, strlen($prefix));
        $relative = str_replace("\\", "/", $relative);

        $file = __DIR__ . '/../app/' . $relative . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
});


