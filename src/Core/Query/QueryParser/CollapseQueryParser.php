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
 * Collapse.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/collapse-and-expand-results.html
 */
final class CollapseQueryParser implements QueryParserInterface
{
    private const TYPE = 'collapse';

    /**
     * @var string|null
     */
    private $min;

    /**
     * @var string|null
     */
    private $max;

    /**
     * @var string|null
     */
    private $sort;

    /**
     * @var string|null
     *
     * options are restricted to: ignore, expand and collapse
     */
    private $nullPolicy;

    /**
     * @var string|null
     */
    private $hint;

    /**
     * @var int|null
     */
    private $size;

    /**
     * @param string|null $min
     * @param string|null $max
     * @param string|null $sort
     * @param string|null $nullPolicy
     * @param string|null $hint
     * @param int|null    $size
     */
    public function __construct(?string $min = null, ?string $max = null, ?string $sort = null, ?string $nullPolicy = null, ?string $hint = null, ?int $size = null)
    {
        $this->min = $min;
        $this->max = $max;
        $this->sort = $sort;
        $this->nullPolicy = $nullPolicy;
        $this->hint = $hint;
        $this->size = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = array_filter(
            [
                'min' => $this->min,
                'max' => $this->max,
                'sort' => $this->sort,
                'nullPolicy' => $this->nullPolicy,
                'hint' => $this->hint,
                'size' => $this->size,
            ],
            static function ($var) {
                return null !== $var && (false === \is_array($var) || 0 !== \count($var));
            }
        );

        return sprintf('!%s %s', self::TYPE, http_build_query($values, '', ' '));
    }
}
