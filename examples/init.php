<?php

use Solarium\Core\Client\Adapter\Curl;
use Symfony\Component\EventDispatcher\EventDispatcher;

error_reporting(E_ALL);
ini_set('display_errors', true);

if (file_exists('config.php')) {
    require('config.php');
}

require $config['autoload'] ?? __DIR__.'/../vendor/autoload.php';

$adapter = new Curl();
$eventDispatcher = new EventDispatcher();

function htmlHeader()
{
    echo '<html><head><title>Solarium examples</title></head><body><nav><a href="index.html">Back to Overview</a></nav><br><article>';
}

function htmlFooter()
{
    echo '</article><br><nav><a href="index.html">Back to Overview</a></nav></body></html>';
}
