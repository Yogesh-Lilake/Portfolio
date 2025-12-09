<?php
namespace app\Core;

use Exception;

class Controller {

    public function view($view, $data = [])
    {
        $path = ROOT_PATH . "app/views/" . $view . ".php";

        if (!file_exists($path)) {
            throw new Exception("View file not found: " . $path);
        }

        // Extract array keys as PHP variables
        if (!empty($data)) extract($data);

        require $path;
    }
}
