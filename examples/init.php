<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

if (file_exists($_SERVER['DOCUMENT_ROOT'] .'/config.php')) {
    require($_SERVER['DOCUMENT_ROOT'] .'/config.php');
}

require $config['autoload'] ?? __DIR__.'/../vendor/autoload.php';

function htmlHeader()
{
    echo '<html><head><title>Solarium examples</title></head><body><nav><a href="index.html">Back to Overview</a></nav><br><article>';
}

function htmlFooter()
{
    echo '</article><br><nav><a href="index.html">Back to Overview</a></nav></body></html>';
}
