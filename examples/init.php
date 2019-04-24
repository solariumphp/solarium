<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

if (file_exists($_SERVER['DOCUMENT_ROOT'] .'/config.php')) {
    require($_SERVER['DOCUMENT_ROOT'] .'/config.php');
    require $config['autoload'];
} else {
    require('config.dist.php');
    require __DIR__.'/vendor/autoload.php';
}


function htmlHeader()
{
    echo '<html><head><title>Solarium examples</title></head><body>';
}

function htmlFooter()
{
    echo '</body></html>';
}
