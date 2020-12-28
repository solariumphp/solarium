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
 * Boolean.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#boolean-query-parser
 */
final class BooleanQueryParser implements QueryParserInterface
{
    private const TYPE = 'bool';

    /**
     * @var string|null
     */
    private $must;

    /**
     * @var string|null
     */
    private $mustNot;

    /**
     * @var string|null
     */
    private $should;

    /**
     * @var string|null
     */
    private $filter;

    /**
     * @var string|null
     */
    private $excludeTags;

    /**
     * @param string|null $must
     * @param string|null $mustNot
     * @param string|null $should
     * @param string|null $filter
     * @param string|null $excludeTags
     */
    public function __construct(?string $must, ?string $mustNot, ?string $should, ?string $filter, ?string $excludeTags)
    {
        $this->must = $must;
        $this->mustNot = $mustNot;
        $this->should = $should;
        $this->filter = $filter;
        $this->excludeTags = $excludeTags;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'must' => $this->must,
                'must_not' => $this->mustNot,
                'should' => $this->should,
                'filter' => $this->filter,
                'excludeTags' => $this->excludeTags,
            ],
            static function ($var) {
                return null !== $var && (false === \is_array($var) || 0 !== \count($var));
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
