<?php
Phar::mapPhar("solarium.phar");
require_once 'phar://solarium.phar/Autoloader.php';
Solarium_Autoloader::register();

__HALT_COMPILER();
?>
