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
 * Function Range.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#function-range-query-parser
 */
final class FunctionRangeQueryParser implements QueryParserInterface
{
    private const TYPE = 'frange';

    /**
     * @var int|null
     */
    private $lower;

    /**
     * @var int|null
     */
    private $upper;

    /**
     * @var bool
     */
    private $includeLower;

    /**
     * @var bool
     */
    private $includeUpper;

    /**
     * @param int|null $lower
     * @param int|null $upper
     * @param bool     $includeLower
     * @param bool     $includeUpper
     */
    public function __construct(?int $lower, ?int $upper, bool $includeLower = true, bool $includeUpper = true)
    {
        $this->lower = $lower;
        $this->upper = $upper;
        $this->includeLower = $includeLower;
        $this->includeUpper = $includeUpper;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'lower' => $this->lower,
                'upper' => $this->upper,
                'incl' => $this->includeLower,
                'incu' => $this->includeUpper,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
