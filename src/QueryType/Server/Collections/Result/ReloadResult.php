<?php

namespace Solarium\QueryType\Server\Collections\Result;

class ReloadResult extends AbstractResult
{
    /**
     * Returns status of the request and the cores that were reloaded when the reload was succesful.
     * @return array status of the request and the cores that were reloaded when the reload was succesful
     */
    public function getStatus(): array
    {
        $this->parseResponse();
        return $this->getData();
    }
}