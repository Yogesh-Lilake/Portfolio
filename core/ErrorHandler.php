<?php

set_exception_handler(function($e){
    app_log("UNCAUGHT EXCEPTION", "critical", [
        "message" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);

    if (defined("DEBUG_MODE") && DEBUG_MODE) {
        echo "<pre style='color:red'>".$e."</pre>";
    } else {
        echo "Unexpected system error.";
    }
});
