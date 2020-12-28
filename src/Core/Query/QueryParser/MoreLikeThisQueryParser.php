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
 * More Like This.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#more-like-this-query-parser
 */
final class MoreLikeThisQueryParser implements QueryParserInterface
{
    private const TYPE = 'mlt';

    /**
     * @var string
     */
    private $fields;

    /**
     * @var int|null
     */
    private $minimumTermFrequency;

    /**
     * @var int|null
     */
    private $minimumDocumentFrequency;

    /**
     * @var int|null
     */
    private $maximumDocumentFrequency;

    /**
     * @var int|null
     */
    private $minimumWordLength;

    /**
     * @var int|null
     */
    private $maximumWordLength;

    /**
     * @var int|null
     */
    private $maximumQueryTerms;

    /**
     * @var int|null
     */
    private $maximumTokens;

    /**
     * @var bool
     */
    private $boost;

    /**
     * MoreLikeThisQueryParser constructor.
     *
     * @param string   $fields
     * @param int|null $minimumTermFrequency
     * @param int|null $minimumDocumentFrequency
     * @param int|null $maximumDocumentFrequency
     * @param int|null $minimumWordLength
     * @param int|null $maximumWordLength
     * @param int|null $maximumQueryTerms
     * @param int|null $maximumTokens
     * @param bool     $boost
     */
    public function __construct(string $fields, ?int $minimumTermFrequency, ?int $minimumDocumentFrequency, ?int $maximumDocumentFrequency, ?int $minimumWordLength, ?int $maximumWordLength, ?int $maximumQueryTerms, ?int $maximumTokens, bool $boost)
    {
        $this->fields = $fields;
        $this->minimumTermFrequency = $minimumTermFrequency;
        $this->minimumDocumentFrequency = $minimumDocumentFrequency;
        $this->maximumDocumentFrequency = $maximumDocumentFrequency;
        $this->minimumWordLength = $minimumWordLength;
        $this->maximumWordLength = $maximumWordLength;
        $this->maximumQueryTerms = $maximumQueryTerms;
        $this->maximumTokens = $maximumTokens;
        $this->boost = $boost;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'qf' => $this->fields,
                'mintf' => $this->minimumTermFrequency,
                'mindf' => $this->minimumDocumentFrequency,
                'maxdf' => $this->maximumDocumentFrequency,
                'minwl' => $this->minimumWordLength,
                'maxwl' => $this->maximumWordLength,
                'maxqt' => $this->maximumQueryTerms,
                'maxntp' => $this->maximumTokens,
                'boost' => $this->boost,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
