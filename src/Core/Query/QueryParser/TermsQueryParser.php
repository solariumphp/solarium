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
 * Terms.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#terms-query-parser
 */
final class TermsQueryParser implements QueryParserInterface
{
    private const TYPE = 'terms';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string|null
     */
    private $separator;

    /**
     * @var string|null
     */
    private $method;

    /**
     * @param string      $field
     * @param string|null $separator
     * @param string|null $method
     */
    public function __construct(string $field, ?string $separator, ?string $method)
    {
        $this->field = $field;
        $this->separator = $separator;
        $this->method = $method;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'f' => $this->field,
                'separator' => $this->separator,
                'method' => $this->method,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
