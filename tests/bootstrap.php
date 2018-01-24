<?php


error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);

// set up an autoloader for PSR-0 class loading
spl_autoload_register(
    function ($class) {
        $paths = array(__DIR__.'/../library', __DIR__);
        foreach ($paths as $path) {
            $filename = $path.'/'.str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
            if (file_exists($filename)) {
                include $filename;
            }
        }
    }
);

require __DIR__.'/../vendor/autoload.php';
