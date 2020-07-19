<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Analytics\Facet;

use Solarium\Core\Configurable;

/**
 * Abstract Facet.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
abstract class AbstractFacet extends Configurable implements \JsonSerializable
{
    use ConfigurableInitTrait;

    /**
     * Value Facets.
     */
    public const TYPE_VALUE = 'value';

    /**
     * Pivot Facets.
     */
    public const TYPE_PIVOT = 'pivot';

    /**
     * Range Facets.
     */
    public const TYPE_RANGE = 'range';

    /**
     * Query Facets.
     */
    public const TYPE_QUERY = 'query';

    /**
     * Map types to subclasses.
     */
    public const CLASSMAP = [
        self::TYPE_VALUE => ValueFacet::class,
        self::TYPE_PIVOT => PivotFacet::class,
        self::TYPE_RANGE => RangeFacet::class,
        self::TYPE_QUERY => QueryFacet::class,
    ];

    /**
     * @var string
     */
    private $key;

    /**
     * Get Facet type.
     */
    abstract public function getType(): string;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }
}
