<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require __DIR__.'/../vendor/autoload.php';

if (file_exists('config.php')) {
    require('config.php');
} else {
    require('config.dist.php');
}


function htmlHeader()
{
    echo '<html><head><title>Solarium examples</title></head><body>';
}

function htmlFooter()
{
    echo '</body></html>';
}
