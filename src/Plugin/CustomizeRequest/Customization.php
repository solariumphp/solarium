<?php

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
    public function setKey($value)
    {
        $this->setOption('key', $value);

        return $this;
    }

    /**
     * Get key value.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getOption('key');
    }

    /**
     * Set type value.
     *
     * @param string $value
     *
     * @return Customization
     */
    public function setType($value)
    {
        $this->setOption('type', $value);

        return $this;
    }

    /**
     * Get type value.
     *
     * @return string
     */
    public function getType()
    {
        return $this->getOption('type');
    }

    /**
     * Set name value.
     *
     * @param string $value
     *
     * @return Customization
     */
    public function setName($value)
    {
        $this->setOption('name', $value);

        return $this;
    }

    /**
     * Get name value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getOption('name');
    }

    /**
     * Set value.
     *
     * @param string $value
     *
     * @return Customization
     */
    public function setValue($value)
    {
        $this->setOption('value', $value);

        return $this;
    }

    /**
     * Get value.
     *
     * @return string
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
     * @return Customization
     */
    public function setPersistent($value)
    {
        $this->setOption('persistent', $value);

        return $this;
    }

    /**
     * Get persistent setting.
     *
     * @return bool
     */
    public function getPersistent()
    {
        return $this->getOption('persistent');
    }

    /**
     * Set overwrite option on/off.
     *
     * @param bool $value
     *
     * @return Customization
     */
    public function setOverwrite($value)
    {
        $this->setOption('overwrite', $value);

        return $this;
    }

    /**
     * Get overwrite option value.
     *
     * @return bool
     */
    public function getOverwrite()
    {
        return $this->getOption('overwrite');
    }

    /**
     * Check for all mandatory settings.
     *
     * @return bool
     */
    public function isValid()
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
