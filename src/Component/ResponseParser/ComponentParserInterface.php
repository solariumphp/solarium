<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Component\AbstractComponent;
use Solarium\Component\ComponentAwareQueryInterface;

/**
 * ComponentParserInterface.
 */
interface ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param ComponentAwareQueryInterface $query
     * @param AbstractComponent            $component
     * @param array                        $data
     *
     * @return object|null
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $component, array $data);
}
