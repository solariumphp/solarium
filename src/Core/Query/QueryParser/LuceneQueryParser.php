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
 * Lucene.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#lucene-query-parser
 */
final class LuceneQueryParser implements QueryParserInterface
{
    private const TYPE = 'lucene';

    /**
     * @var string
     */
    private $operator;

    /**
     * @var string
     */
    private $defaultField;

    /**
     * @param string $operator
     * @param string $defaultField
     */
    public function __construct(string $operator, string $defaultField)
    {
        $this->operator = $operator;
        $this->defaultField = $defaultField;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'q.op' => $this->operator,
                'df' => $this->defaultField,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
