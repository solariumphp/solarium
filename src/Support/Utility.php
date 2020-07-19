<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Support;

/**
 * Utility.
 */
class Utility
{
    /**
     * Extracts the encoding from the XML declaration of a file if present.
     *
     * @param string $file
     *
     * @return string|null
     */
    public static function getXmlEncoding(string $file): ?string
    {
        $encoding = null;

        $xml = file_get_contents($file);

        if (false !== $xml) {
            // discard UTF-8 Byte Order Mark
            if (0 === strpos($xml, pack('CCC', 0xEF, 0xBB, 0xBF))) {
                $xml = substr($xml, 3);
            }

            // detect XML declaration
            if (0 === strpos($xml, '<?xml')) {
                $declaration = substr($xml, 0, strpos($xml, '?>') + 2);

                // detect encoding attribute
                if (false !== $pos = strpos($declaration, 'encoding="')) {
                    $encoding = substr($declaration, $pos + 10, strpos($declaration, '"', $pos + 10) - $pos - 10);
                }
            }
        }

        return $encoding;
    }
}
