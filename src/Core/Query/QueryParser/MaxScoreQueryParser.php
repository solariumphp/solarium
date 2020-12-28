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
 * Max Score.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#max-score-query-parser
 */
final class MaxScoreQueryParser implements QueryParserInterface
{
    private const TYPE = 'maxscore';

    /**
     * @var float
     */
    private $tie;

    /**
     * @param float $tie
     */
    public function __construct(float $tie)
    {
        $this->tie = $tie;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'tie' => $this->tie,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
