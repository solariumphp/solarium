<?php

// Create a new htrouter phar file
@unlink('solarium.phar');
$phar = new Phar('solarium.phar', 0, 'solarium.phar');

$phar->setStub(file_get_contents("stub.php"));

$basePath = realpath(__DIR__."/../library/Solarium");
$phar->buildFromDirectory($basePath, '/\.php$/');


