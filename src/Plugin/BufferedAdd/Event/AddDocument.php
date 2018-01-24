<?php

namespace Solarium\Plugin\BufferedAdd\Event;

use Solarium\QueryType\Select\Result\DocumentInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * AddDocument event, see Events for details.
 */
class AddDocument extends Event
{
    /**
     * @var DocumentInterface
     */
    protected $document;

    /**
     * Event constructor.
     *
     * @param DocumentInterface $document
     */
    public function __construct($document)
    {
        $this->document = $document;
    }

    /**
     * Get the result for this event.
     *
     * @return DocumentInterface
     */
    public function getDocument()
    {
        return $this->document;
    }
}
