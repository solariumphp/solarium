<?php

namespace Solarium\QueryType\Server\Collections\Result;

class DeleteResult extends AbstractResult
{
    /**
     * Returns status of the request and the cores that were deleted.
     *
     * @return array status of the request and the cores that were deleted
     */
    public function getStatus(): array
    {
        $this->parseResponse();
        return $this->getData();
    }
}
