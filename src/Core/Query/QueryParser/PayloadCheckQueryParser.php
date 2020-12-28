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
 * Payload Check.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#payload-check-parser
 */
final class PayloadCheckQueryParser implements QueryParserInterface
{
    private const TYPE = 'payload_check';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $payloads;

    /**
     * @param string $field
     * @param string $payloads
     */
    public function __construct(string $field, string $payloads)
    {
        $this->field = $field;
        $this->payloads = $payloads;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'f' => $this->field,
                'payloads' => $this->payloads,
            ],
            static function ($var) {
                return null !== $var;
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
