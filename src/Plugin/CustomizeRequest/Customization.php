<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\CustomizeRequest;

use Solarium\Core\Configurable;

/**
 * Customization value object.
 */
class Customization extends Configurable
{
    /**
     * Type definition for params.
     */
    const TYPE_PARAM = 'param';

    /**
     * Type definition for headers.
     */
    const TYPE_HEADER = 'header';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'key' => null,
        'type' => null,
        'name' => null,
        'value' => null,
        'persistent' => false,
        'overwrite' => true,
    ];

    /**
     * Set key value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setKey(string $value): self
    {
        $this->setOption('key', $value);

        return $this;
    }

    /**
     * Get key value.
     *
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->getOption('key');
    }

    /**
     * Set type value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setType(string $value): self
    {
        $this->setOption('type', $value);

        return $this;
    }

    /**
     * Get type value.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getOption('type');
    }

    /**
     * Set name value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setName(string $value): self
    {
        $this->setOption('name', $value);

        return $this;
    }

    /**
     * Get name value.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getOption('name');
    }

    /**
     * Set value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setValue(string $value): self
    {
        $this->setOption('value', $value);

        return $this;
    }

    /**
     * Get value.
     *
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->getOption('value');
    }

    /**
     * Set persistent on/off.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setPersistent(bool $value): self
    {
        $this->setOption('persistent', $value);

        return $this;
    }

    /**
     * Get persistent setting.
     *
     * @return bool|null
     */
    public function getPersistent(): ?bool
    {
        return $this->getOption('persistent');
    }

    /**
     * Set overwrite option on/off.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setOverwrite(bool $value): self
    {
        $this->setOption('overwrite', $value);

        return $this;
    }

    /**
     * Get overwrite option value.
     *
     * @return bool|null
     */
    public function getOverwrite(): ?bool
    {
        return $this->getOption('overwrite');
    }

    /**
     * Check for all mandatory settings.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $type = $this->getType();
        if (self::TYPE_PARAM !== $type && self::TYPE_HEADER !== $type) {
            return false;
        }

        if (null === $this->getKey() || null === $this->getName() || null === $this->getValue()) {
            return false;
        }

        return true;
    }
}
