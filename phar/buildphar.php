<?php
/**
 * Copyright 2012 Bas de Nooijer. All rights reserved.
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
 * @copyright Copyright 2012 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 */


// Check phar.readonly ini setting
if (ini_get('phar.readonly') == '1') {
    throw new \RuntimeException("Your php.ini has phar.readonly enabled. Phar cannot be created. Please alter your php.ini first.\n");
}

// You can optionally use arguments to enable compression and whitespace/comment stripping.
// Example: "php buildphar.php -s 1 -c 1"
// -c with a value of 1 enables compression, multiple phars will be created
// -s with a value of 1 enables stripping
$options = getopt('s:c:');
$compress = (isset($options['c']) && $options['c'] == '1');
$strip = (isset($options['s']) && $options['s'] == '1');

$start = microtime(true);

// Create a new Solarium PHAR file.
@unlink('solarium.phar');
$phar = new Phar('solarium.phar', 0, 'solarium.phar');
$phar->setStub(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stub.php'));
$phar->setSignatureAlgorithm(Phar::SHA1);

// Add files to the PHAR.
$basePath = dirname(__DIR__);
$directoryIterator = new AppendIterator();
$directoryIterator->append(
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($basePath . DIRECTORY_SEPARATOR . 'library'),
        RecursiveIteratorIterator::SELF_FIRST
    )
);
$directoryIterator->append(
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($basePath . DIRECTORY_SEPARATOR . 'vendor'),
        RecursiveIteratorIterator::SELF_FIRST
    )
);

if ($strip) {
    foreach ($directoryIterator as $file) {
        if (0 !== preg_match('/\\.php$/i', $file)) {
            $phar->addFromString(substr($file, strlen($basePath) + 1), php_strip_whitespace($file));
        }
    }
} else {
    $phar->buildFromIterator($directoryIterator, $basePath);
}

// Create compressed versions
if ($compress) {
    $phar->compress(Phar::BZ2);
    $phar->compress(Phar::GZ);
}

$time = round(microtime(true)-$start, 5);
echo "\nDONE ($time seconds)\nsolarium.phar has been created.\n\n";
