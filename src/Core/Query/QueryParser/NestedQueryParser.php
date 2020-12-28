<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query\QueryParser;

/**
 * Nested.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#nested-query-parser
 */
final class NestedQueryParser implements QueryParserInterface
{
    private const TYPE = 'query';

    /**
     * @var string
     */
    private $defType;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $defType
     * @param string $value
     */
    public function __construct(string $defType, string $value)
    {
        $this->defType = $defType;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'defType' => $this->defType,
                'v' => $this->value,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
