<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Support;

use Solarium\Exception\UnexpectedValueException;

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

    /**
     * Check whether a value is a valid point value for spatial search.
     *
     * Example: '45.15,-93.85' (geodetic & non-geodetic PointType)
     *
     * Example: '45.15 -93.85' (non-geodetic RPT)
     *
     * @param string $value
     *
     * @return bool
     */
    public static function isPointValue(string $value): bool
    {
        return (bool) preg_match('/^-?\d+(?:\.\d+)?[, ]-?\d+(?:\.\d+)?$/', $value);
    }

    /**
     * Check whether a field name is a wildcard pattern.
     *
     * Wildcards are used in {@see https://solr.apache.org/guide/dynamic-fields.html dynamicField}
     * and {@see https://solr.apache.org/guide/copying-fields.html copyField} definitions.
     *
     * @param string $fieldName
     *
     * @return bool
     */
    public static function isWildcardPattern(string $fieldName): bool
    {
        return 1 === substr_count($fieldName, '*') && preg_match('/^\*|\*$/', $fieldName);
    }

    /**
     * Check whether a field name matches a wildcard pattern.
     *
     * Wildcards are used in {@see https://solr.apache.org/guide/dynamic-fields.html dynamicField}
     * and {@see https://solr.apache.org/guide/copying-fields.html copyField} definitions.
     *
     * @param string $wildcardPattern
     * @param string $fieldName
     *
     * @throws UnexpectedValueException
     *
     * @return bool
     */
    public static function fieldMatchesWildcard(string $wildcardPattern, string $fieldName): bool
    {
        if (!self::isWildcardPattern($wildcardPattern)) {
            throw new UnexpectedValueException('Wildcard pattern must have a "*" only at the start or the end.');
        }

        $match = false;

        if ('*' === $wildcardPattern) {
            $match = true;
        } elseif (0 === strpos($wildcardPattern, '*')) {
            $match = substr($wildcardPattern, 1) === substr($fieldName, 1 - \strlen($wildcardPattern));
        } else {
            $match = 0 === strpos($fieldName, substr($wildcardPattern, 0, -1));
        }

        return $match;
    }

    /**
     * Returns a compact display version of the (Java) FQCN of a Solr component.
     *
     * The package hierachy is abbreviated to one-letter prefixes. This compact
     * representation is purely informative. Different package hierarchies can
     * have overlapping abbreviations.
     *
     * Example: 'org.apache.solr.schema.TextField'
     *  becomes 'o.a.s.s.TextField'
     * Example: 'org.apache.solr.search.similarities.SchemaSimilarityFactory$SchemaSimilarity'
     *  becomes 'o.a.s.s.s.SchemaSimilarityFactory$SchemaSimilarity'
     *
     * @param string $className
     *
     * @return string
     */
    public static function compactSolrClassName(string $className): string
    {
        return preg_replace('/(?<=[a-z1-9])[a-z1-9]+(?=\.)/i', '', $className);
    }
}
