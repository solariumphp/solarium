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
 * Filters.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#filters-query-parser
 */
final class FiltersQueryParser implements QueryParserInterface
{
    private const TYPE = 'filters';

    /**
     * @var string
     */
    private $param;

    /**
     * @var string|null
     */
    private $excludeTags;

    /**
     * @param string      $param
     * @param string|null $excludeTags
     */
    public function __construct(string $param, ?string $excludeTags)
    {
        $this->param = $param;
        $this->excludeTags = $excludeTags;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'param' => $this->param,
                'excludeTags' => $this->excludeTags,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
