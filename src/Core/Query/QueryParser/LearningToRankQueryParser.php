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
 * Learning To Rank.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#learning-to-rank-query-parser
 */
final class LearningToRankQueryParser implements QueryParserInterface
{
    private const TYPE = 'ltr';

    /**
     * @var string
     */
    private $model;

    /**
     * @var int
     */
    private $reRankDocs;

    /**
     * @param string $model
     * @param int    $reRankDocs
     */
    public function __construct(string $model, int $reRankDocs)
    {
        $this->model = $model;
        $this->reRankDocs = $reRankDocs;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'model' => $this->model,
                'reRankDocs' => $this->reRankDocs,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
