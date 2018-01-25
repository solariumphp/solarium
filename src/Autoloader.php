<?php

namespace Solarium;

/**
 * Autoloader.
 *
 * This class is included to allow for easy usage of Solarium in environments missing a PSR-O autoloader.
 *
 * It's recommended to install Solarium using composer, which will also provide autoloading for you. In that
 * case you don't need to use this autoloader.
 *
 * Solarium is PSR-0 compliant, so you can also use any other compatible autoloader
 * (most modern frameworks include one)
 */
class Autoloader
{
    /**
     * Register the Solarium autoloader.
     *
     * The autoloader only acts for classnames that start with 'Solarium'. It
     * will be appended to any other autoloaders already registered.
     *
     * Using this autoloader will disable any existing __autoload function. If
     * you want to use multiple autoloaders please use spl_autoload_register.
     *
     * @static
     */
    public static function register()
    {
        spl_autoload_register([new self(), 'load']);
    }

    /**
     * Autoload a class.
     *
     * This method is automatically called after registering this autoloader.
     * The autoloader only acts for classnames that start with 'Solarium'.
     *
     * @static
     *
     * @param string $class
     */
    public static function load($class)
    {
        if ('Solarium' == substr($class, 0, 8)) {
            $class = str_replace(
                ['Solarium', '\\'],
                ['', '/'],
                $class
            );

            $file = __DIR__.$class.'.php';

            require $file;
        }
    }
}
