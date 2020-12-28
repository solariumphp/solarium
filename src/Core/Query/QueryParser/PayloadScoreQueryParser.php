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
 * Payload.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#payload-query-parsers
 */
final class PayloadScoreQueryParser implements QueryParserInterface
{
    private const TYPE = 'payload_score';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $function;

    /**
     * @var string|null
     */
    private $operator;

    /**
     * @var bool|null
     */
    private $includeSpanScore;

    /**
     * @param string      $field
     * @param string      $function
     * @param string|null $operator
     * @param bool|null   $includeSpanScore
     */
    public function __construct(string $field, string $function, ?string $operator, ?bool $includeSpanScore)
    {
        $this->field = $field;
        $this->function = $function;
        $this->operator = $operator;
        $this->includeSpanScore = $includeSpanScore;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'f' => $this->field,
                'func' => $this->function,
                'operator' => $this->operator,
                'includeSpanScore' => $this->includeSpanScore,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
