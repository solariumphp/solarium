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
 * Block Join Children.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#block-join-children-query-parser
 */
final class BlockJoinChildQueryParser implements QueryParserInterface
{
    private const TYPE = 'child';

    /**
     * @var string
     */
    private $of;

    /**
     * @var string|null
     */
    private $filters;

    /**
     * @var string|null
     */
    private $excludeTags;

    /**
     * @param string      $of
     * @param string|null $filters
     * @param string|null $excludeTags
     */
    public function __construct(string $of, ?string $filters, ?string $excludeTags)
    {
        $this->of = $of;
        $this->filters = $filters;
        $this->excludeTags = $excludeTags;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'of' => $this->of,
                'filters' => $this->filters,
                'excludeTags' => $this->excludeTags,
            ],
            static function ($var) {
                return null !== $var && (false === \is_array($var) || 0 !== \count($var));
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
