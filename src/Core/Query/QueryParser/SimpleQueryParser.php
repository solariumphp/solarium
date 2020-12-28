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
 * Simple.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#simple-query-parser
 */
final class SimpleQueryParser implements QueryParserInterface
{
    /**
     * @var string|null
     */
    private $operators;

    /**
     * @var string|null
     */
    private $operator;

    /**
     * @var string|null
     */
    private $fields;

    /**
     * @var string|null
     */
    private $defaultField;

    /**
     * @param string|null $operators
     * @param string|null $operator
     * @param string|null $fields
     * @param string|null $defaultField
     */
    public function __construct(?string $operators, ?string $operator, ?string $fields, ?string $defaultField)
    {
        $this->operators = $operators;
        $this->operator = $operator;
        $this->fields = $fields;
        $this->defaultField = $defaultField;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'q.operators' => $this->operators,
                'q.op' => $this->operator,
                'qf' => $this->fields,
                'df' => $this->defaultField,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('%s', http_build_query($values, '', ' '));
    }
}
