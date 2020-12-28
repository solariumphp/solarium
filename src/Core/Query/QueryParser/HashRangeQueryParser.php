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
 * Hash Range.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#hash-range-query-parser
 */
final class HashRangeQueryParser implements QueryParserInterface
{
    private const TYPE = 'hash_range';

    /**
     * @var string
     */
    private $field;

    /**
     * @var int
     */
    private $lower;

    /**
     * @var int
     */
    private $upper;

    /**
     * @param string $field
     * @param int    $lower
     * @param int    $upper
     */
    public function __construct(string $field, int $lower, int $upper)
    {
        $this->field = $field;
        $this->lower = $lower;
        $this->upper = $upper;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'f' => $this->field,
                'l' => $this->lower,
                'u' => $this->upper,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
