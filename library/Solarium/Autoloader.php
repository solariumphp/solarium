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
 *
 * @package Solarium
 */

/**
 * Autoloader
 *
 * This class is included to allow for easy usage of Solarium. If you already
 * have your own autoloader that follows the Zend Framework class/file naming
 * you can use that to autoload Solarium (for instance Zend_Loader).
 *
 * @package Solarium
 */
class Solarium_Autoloader
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
    static public function register()
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
     * @param string $class
     * @return void
     */
    static public function load($class)
    {
        if (substr($class, 0, 8) == 'Solarium') {

            $class = str_replace(
                array('Solarium', '_'),
                array('', '/'),
                $class
            );
            
            $file = dirname(__FILE__) . '/' . $class . '.php';

            require($file);
        }
    }

}