<?php

namespace Solarium\Support;

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
            if (pack('CCC', 0xEF, 0xBB, 0xBF) === substr($xml, 0, 3)) {
                $xml = substr($xml, 3);
            }

            // detect XML declaration
            if ('<?xml' === substr($xml, 0, 5)) {
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
