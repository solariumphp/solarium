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
 * ReRank.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/query-re-ranking.html#rerank-query-parser
 */
final class ReRankQueryParser implements QueryParserInterface
{
    private const TYPE = 'rerank';

    /**
     * @var string
     */
    private $reRankQuery;

    /**
     * @var int|null
     */
    private $reRankDocs;

    /**
     * @var int|null
     */
    private $reRankWeight;

    /**
     * @param string   $reRankQuery
     * @param int|null $reRankDocs
     * @param int|null $reRankWeight
     */
    public function __construct(string $reRankQuery, ?int $reRankDocs, ?int $reRankWeight)
    {
        $this->reRankQuery = $reRankQuery;
        $this->reRankDocs = $reRankDocs;
        $this->reRankWeight = $reRankWeight;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'reRankQuery' => $this->reRankQuery,
                'reRankDocs' => $this->reRankDocs,
                'reRankWeight' => $this->reRankWeight,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
