<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query\LocalParameters;

/**
 * Local Parameter.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class LocalParameter implements LocalParameterInterface
{
    public const TYPE_KEY = 'key';

    public const TYPE_EXCLUDE = 'ex';

    public const TYPE_RANGE = 'range';

    public const TYPE_TAG = 'tag';

    public const TYPE_TERM = 'terms';

    public const TYPE_QUERY = 'query';

    public const TYPE_STAT = 'stats';

    public const TYPE_MIN = 'min';

    public const TYPE_MAX = 'max';

    public const TYPE_MEAN = 'mean';

    public const TYPE_DEFAULT_FIELD = 'df';

    public const TYPE_QUERY_FIELD = 'qf';

    public const TYPE_TYPE = 'type';

    public const TYPE_VALUE = 'v';

    public const TYPE_CACHE = 'cache';

    public const TYPE_COST = 'cost';

    public const PARAMETER_MAP = [
        self::TYPE_KEY => 'local_key',
        self::TYPE_EXCLUDE => 'local_exclude',
        self::TYPE_TAG => 'local_tag',
        self::TYPE_RANGE => 'local_range',
        self::TYPE_STAT => 'local_stats',
        self::TYPE_TERM => 'local_terms',
        self::TYPE_TYPE => 'local_type',
        self::TYPE_QUERY => 'local_query',
        self::TYPE_QUERY_FIELD => 'local_query_field',
        self::TYPE_DEFAULT_FIELD => 'local_default_field',
        self::TYPE_MAX => 'local_max',
        self::TYPE_MEAN => 'local_mean',
        self::TYPE_MIN => 'local_min',
        self::TYPE_VALUE => 'local_value',
        self::TYPE_CACHE => 'local_cache',
        self::TYPE_COST => 'local_cost',
    ];

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $values = [];

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        if (0 === \count($this->values) || '' === key($this->values)) {
            return '';
        }

        if (1 === \count($this->values)) {
            return sprintf('%s=%s', $this->type, key($this->values));
        }

        return sprintf('%s=%s', $this->getType(), implode(',', $this->getValues()));
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param array $values
     *
     * @return \Solarium\Core\Query\LocalParameters\LocalParameterInterface
     */
    public function setValues(array $values): LocalParameterInterface
    {
        $this->clearValues();

        return $this->addValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(): array
    {
        return array_keys($this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function clearValues(): LocalParameterInterface
    {
        $this->values = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addValue($value): LocalParameterInterface
    {
        $this->values[$value] = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addValues(array $values): LocalParameterInterface
    {
        foreach ($values as $value) {
            $this->addValue($value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeValue($value): LocalParameterInterface
    {
        if (true === isset($this->values[$value])) {
            unset($this->values[$value]);
        }

        return $this;
    }
}
