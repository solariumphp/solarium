<?php

namespace Solarium\Plugin\CustomizeRequest;

use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreExecuteRequest as preExecuteRequestEvent;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\RuntimeException;

/**
 * CustomizeRequest plugin.
 *
 * You can use this plugin to customize the requests generated for Solarium queries by adding or overwriting
 * params and/or headers.
 */
class CustomizeRequest extends AbstractPlugin
{
    /**
     * Holds customizations added to this plugin.
     *
     * @var Customization[]
     */
    protected $customizations = [];

    /**
     * Create a Customization instance.
     *
     * If you supply a string as the first arguments ($options) it will be used as the key for the Customization
     * and it will be added to this plugin.
     * If you supply an options array/object that contains a key the Customization will also be added to the plugin.
     *
     * When no key is supplied the Customization cannot be added, in that case you will need to add it manually
     * after setting the key, by using the addCustomization method.
     *
     * @param mixed $options
     *
     * @return Customization
     */
    public function createCustomization($options = null)
    {
        if (is_string($options)) {
            $fq = new Customization();
            $fq->setKey($options);
        } else {
            $fq = new Customization($options);
        }

        if (null !== $fq->getKey()) {
            $this->addCustomization($fq);
        }

        return $fq;
    }

    /**
     * Add a customization.
     *
     * Supports a Customization instance or a config array, in that case a new
     * Customization instance wil be created based on the options.
     *
     *
     * @param Customization|array $customization
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addCustomization($customization)
    {
        if (is_array($customization)) {
            $customization = new Customization($customization);
        }

        $key = $customization->getKey();

        // check for non-empty key
        if (0 === strlen($key)) {
            throw new InvalidArgumentException('A Customization must have a key value');
        }

        // check for a unique key
        if (array_key_exists($key, $this->customizations)) {
            //double add calls for the same customization are ignored, others cause an exception
            if ($this->customizations[$key] !== $customization) {
                throw new InvalidArgumentException('A Customization must have a unique key value');
            }
        }

        $this->customizations[$key] = $customization;

        return $this;
    }

    /**
     * Add multiple Customizations.
     *
     * @param array $customizations
     *
     * @return CustomizeRequest Provides fluent interface
     */
    public function addCustomizations(array $customizations)
    {
        foreach ($customizations as $key => $customization) {
            // in case of a config array: add key to config
            if (is_array($customization) && !isset($customization['key'])) {
                $customization['key'] = $key;
            }

            $this->addCustomization($customization);
        }

        return $this;
    }

    /**
     * Get a Customization.
     *
     * @param string $key
     *
     * @return string
     */
    public function getCustomization($key)
    {
        if (isset($this->customizations[$key])) {
            return $this->customizations[$key];
        }
    }

    /**
     * Get all Customizations.
     *
     * @return Customization[]
     */
    public function getCustomizations()
    {
        return $this->customizations;
    }

    /**
     * Remove a single Customization.
     *
     * You can remove a Customization by passing its key, or by passing the Customization instance.
     *
     * @param string|Customization $customization
     *
     * @return CustomizeRequest Provides fluent interface
     */
    public function removeCustomization($customization)
    {
        if (is_object($customization)) {
            $customization = $customization->getKey();
        }

        if (isset($this->customizations[$customization])) {
            unset($this->customizations[$customization]);
        }

        return $this;
    }

    /**
     * Remove all Customizations.
     *
     * @return CustomizeRequest Provides fluent interface
     */
    public function clearCustomizations()
    {
        $this->customizations = [];

        return $this;
    }

    /**
     * Set multiple Customizations.
     *
     * This overwrites any existing Customizations
     *
     * @param array $customizations
     */
    public function setCustomizations($customizations)
    {
        $this->clearCustomizations();
        $this->addCustomizations($customizations);
    }

    /**
     * Event hook to customize the request object.
     *
     *
     * @param preExecuteRequestEvent $event
     *
     * @throws RuntimeException
     */
    public function preExecuteRequest(preExecuteRequestEvent $event)
    {
        $request = $event->getRequest();
        foreach ($this->getCustomizations() as $key => $customization) {
            // first validate
            if (!$customization->isValid()) {
                throw new RuntimeException('Request customization with key "'.$key.'" is invalid');
            }

            // apply to request, depending on type
            switch ($customization->getType()) {
                case Customization::TYPE_PARAM:
                    $request->addParam(
                        $customization->getName(),
                        $customization->getValue(),
                        $customization->getOverwrite()
                    );
                    break;
                case Customization::TYPE_HEADER:
                    $request->addHeader($customization->getName().': '.$customization->getValue());
                    break;
            }

            // remove single-use customizations after use
            if (!$customization->getPersistent()) {
                $this->removeCustomization($key);
            }
        }

        $event->setRequest($request);
    }

    /**
     * Initialize options.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'customization':
                    $this->addCustomizations($value);
                    break;
            }
        }
    }

    /**
     * Plugin init function.
     *
     * Register event listeners
     */
    protected function initPluginType()
    {
        $dispatcher = $this->client->getEventDispatcher();
        $dispatcher->addListener(Events::PRE_EXECUTE_REQUEST, [$this, 'preExecuteRequest']);
    }
}
