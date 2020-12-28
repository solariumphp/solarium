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
 * Complex Phrase.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#complex-phrase-query-parser
 */
final class ComplexPhraseQueryParser implements QueryParserInterface
{
    private const TYPE = 'complexphrase';

    /**
     * @var bool|null
     */
    private $inOrder;

    /**
     * @var string|null
     */
    private $df;

    /**
     * @param bool|null   $inOrder
     * @param string|null $df
     */
    public function __construct(?bool $inOrder, ?string $df)
    {
        $this->inOrder = $inOrder;
        $this->df = $df;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'inOrder' => $this->inOrder,
                'df' => $this->df,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
