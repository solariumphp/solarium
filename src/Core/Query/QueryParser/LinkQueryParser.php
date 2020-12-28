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
 * Link.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#join-query-parser
 */
final class LinkQueryParser implements QueryParserInterface
{
    private const TYPE = 'join';

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var string|null
     */
    private $fromIndex;

    /**
     * @var string|null
     *
     * options include avg (average), max (maximum), min (minimum), total (total), or none
     */
    private $score;

    /**
     * @var string|null
     *
     * options are restricted to: index, dvWithScore, and topLevelDV
     */
    private $method;

    /**
     * @param string      $from
     * @param string      $to
     * @param string|null $fromIndex
     * @param string|null $score
     * @param string|null $method
     */
    public function __construct(string $from, string $to, ?string $fromIndex, ?string $score, ?string $method)
    {
        $this->from = $from;
        $this->to = $to;
        $this->fromIndex = $fromIndex;
        $this->score = $score;
        $this->method = $method;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'from' => $this->from,
                'to' => $this->to,
                'fromIndex' => $this->fromIndex,
                'score' => $this->score,
                'method' => $this->method,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
