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
 * Graph.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#graph-query-parser
 */
final class GraphQueryParser implements QueryParserInterface
{
    private const TYPE = 'graph';

    /**
     * @var string|null
     */
    private $to;

    /**
     * @var string|null
     */
    private $from;

    /**
     * @var string|null
     */
    private $traversalFilter;

    /**
     * @var int|null
     */
    private $maxDepth;

    /**
     * @var bool|null
     */
    private $returnRoot;

    /**
     * @var bool|null
     */
    private $returnOnlyLeaf;

    /**
     * @var bool|null
     */
    private $useAutomations;

    /**
     * @param string|null $to
     * @param string|null $from
     * @param string|null $traversalFilter
     * @param int|null    $maxDepth
     * @param bool|null   $returnRoot
     * @param bool|null   $returnOnlyLeaf
     * @param bool|null   $useAutomations
     */
    public function __construct(?string $to, ?string $from, ?string $traversalFilter, ?int $maxDepth, ?bool $returnRoot, ?bool $returnOnlyLeaf, ?bool $useAutomations)
    {
        $this->to = $to;
        $this->from = $from;
        $this->traversalFilter = $traversalFilter;
        $this->maxDepth = $maxDepth;
        $this->returnRoot = $returnRoot;
        $this->returnOnlyLeaf = $returnOnlyLeaf;
        $this->useAutomations = $useAutomations;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'to' => $this->to,
                'from' => $this->from,
                'traversalFilter' => $this->traversalFilter,
                'maxDepth' => $this->maxDepth,
                'returnRoot' => $this->returnRoot,
                'returnOnlyLeaf' => $this->returnOnlyLeaf,
                'useAutn' => $this->useAutomations,
            ],
            static function ($var) {
                return null !== $var && (false === \is_array($var) || 0 !== \count($var));
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
