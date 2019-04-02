<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

trait CoreActionTrait
{
    /**
     * Set the core name that should be reloaded.
     *
     * @param string $core
     *
     * @return self
     */
    public function setCore(string $core): CoreActionInterface
    {
        $this->setOption('core', $core);
        return $this;
    }

    /**
     * Get the related core name.
     *
     * @return string|null
     */
    public function getCore(): ?string
    {
        return $this->getOption('core');
    }
}
