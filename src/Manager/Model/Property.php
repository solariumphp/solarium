<?php

declare(strict_types=1);

namespace Solarium\Manager\Model;

/**
 * Property.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Property implements \JsonSerializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __construct(string $name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            $this->name => $this->value,
        ];
    }
}
