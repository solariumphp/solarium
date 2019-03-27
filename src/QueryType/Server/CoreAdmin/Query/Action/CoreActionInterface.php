<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\Query\Action\ActionInterface;

interface CoreActionInterface extends ActionInterface
{
    /**
     * Set the core name that should be reloaded.
     *
     * @param string $core
     */
    public function setCore(string $core);

    /**
     * Get the related core name.
     *
     * @return string
     */
    public function getCore(): string;
}
