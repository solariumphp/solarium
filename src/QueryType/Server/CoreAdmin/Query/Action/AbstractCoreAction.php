<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\Core\Configurable;

abstract class AbstractCoreAction extends Configurable
{
    /**
     * Set the core name that should be reloaded.
     *
     * @param string $core
     */
    public function setCore(string $core)
    {
        $this->setOption('core', $core);
    }

    /**
     * Get the related core name.
     *
     * @return string
     */
    public function getCore(): string
    {
        return (string) $this->getOption('core');
    }
}
