<?php

namespace Solarium\QueryType\Server\Collections\Result;

class CreateResult extends AbstractResult
{
    /**
     * Returns the status of the request and the new core names.
     *
     * @return array status of the request and the new core names
     */
    public function getStatus(): array
    {
        $this->parseResponse();
        return $this->getData();
    }
}
