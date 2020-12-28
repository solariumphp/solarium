<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query\QueryParser;

use Solarium\Core\Query\QueryParser\Model\SwitchCase;

/**
 * Switch.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/other-parsers.html#switch-query-parser
 */
final class SwitchQueryParser implements QueryParserInterface
{
    private const TYPE = 'switch';

    /**
     * @var \Solarium\Core\Query\QueryParser\Model\SwitchCase[]
     */
    private $cases;

    /**
     * @var string|null
     */
    private $default;

    /**
     * @param \Solarium\Core\Query\QueryParser\Model\SwitchCase[] $cases
     * @param string|null                                         $default
     */
    public function __construct(array $cases, ?string $default)
    {
        $this->cases = $cases;
        $this->default = $default;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'cases' => $this->cases,
                'default' => $this->default,
            ],
            static function ($var) {
                return null !== $var && (false === \is_array($var) || 0 !== \count($var));
            }
        );

        $string = sprintf('!%s', self::TYPE);

        if (isset($values['cases'])) {
            $string .= sprintf(' %s', implode(' ', array_map('strval', $values['cases'])));
        }

        if (isset($values['default'])) {
            $string .= sprintf(' default=%s', $values['default']);
        }

        return $string;
    }

    /**
     * @param \Solarium\Core\Query\QueryParser\Model\SwitchCase $case
     *
     * @return $this
     */
    public function addCase(SwitchCase $case): self
    {
        $this->cases[] = $case;

        return $this;
    }
}
