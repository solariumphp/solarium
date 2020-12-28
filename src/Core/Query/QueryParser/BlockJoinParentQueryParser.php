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
 * Block Join Parent.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#block-join-parent-query-parser
 */
final class BlockJoinParentQueryParser implements QueryParserInterface
{
    private const TYPE = 'parent';

    /**
     * @var string
     */
    private $which;

    /**
     * @var string|null
     */
    private $filters;

    /**
     * @var string|null
     */
    private $excludeTags;

    /**
     * @var string|null
     *
     * options are avg (average), max (maximum), min (minimum), total (sum)
     */
    private $score;

    /**
     * @param string      $which
     * @param string|null $filters
     * @param string|null $excludeTags
     * @param string|null $score
     */
    public function __construct(string $which, ?string $filters, ?string $excludeTags, ?string $score)
    {
        $this->which = $which;
        $this->filters = $filters;
        $this->excludeTags = $excludeTags;
        $this->score = $score;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'which' => $this->which,
                'filters' => $this->filters,
                'excludeTags' => $this->excludeTags,
                'score' => $this->score,
            ],
            static function ($var) {
                return null !== $var && (false === \is_array($var) || 0 !== \count($var));
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
