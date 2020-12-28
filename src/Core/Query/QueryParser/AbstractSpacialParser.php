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
 * Abstractc Spacial Parser.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/spatial-search.html#searching-with-query-parsers
 */
class AbstractSpacialParser implements QueryParserInterface
{
    protected const TYPE = '';

    /**
     * @var string|null
     */
    private $distance;

    /**
     * @var string|null
     */
    private $pointType;

    /**
     * @var string
     */
    private $spatialField;

    /**
     * @var string|null
     */
    private $score;

    /**
     * @var string|null
     */
    private $filter;

    /**
     * @param string      $spatialField
     * @param string|null $distance
     * @param string|null $pointType
     * @param string|null $score
     * @param string|null $filter
     */
    public function __construct(string $spatialField, ?string $distance, ?string $pointType, ?string $score, ?string $filter)
    {
        $this->distance = $distance;
        $this->pointType = $pointType;
        $this->spatialField = $spatialField;
        $this->score = $score;
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'd' => $this->distance,
                'pt' => $this->pointType,
                'sfield' => $this->spatialField,
                'score' => $this->score,
                'filter' => $this->filter,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', static::TYPE, http_build_query($values, '', ' '));
    }
}
