<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium;

/**
 * Autoloader
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
     * Register the Solarium autoloader
     *
     * The autoloader only acts for classnames that start with 'Solarium'. It
     * will be appended to any other autoloaders already registered.
     *
     * Using this autoloader will disable any existing __autoload function. If
     * you want to use multiple autoloaders please use spl_autoload_register.
     *
     * @static
     * @return void
     */
    public static function register()
    {
        spl_autoload_register(array(new self, 'load'));
    }

    /**
     * Autoload a class
     *
     * This method is automatically called after registering this autoloader.
     * The autoloader only acts for classnames that start with 'Solarium'.
     *
     * @static
     * @param  string $class
     * @return void
     */
    public static function load($class)
    {
        if (substr($class, 0, 8) == 'Solarium') {

            $class = str_replace(
                array('Solarium', '\\'),
                array('', '/'),
                $class
            );

            $file = dirname(__FILE__) . $class . '.php';

            require($file);
        }
    }
}
