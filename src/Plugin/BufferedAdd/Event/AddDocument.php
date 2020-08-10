<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedAdd\Event;

use Solarium\Core\Query\DocumentInterface;
use Symfony\Contracts\EventDispatcher\Event;

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
    public function __construct(DocumentInterface $document)
    {
        $this->document = $document;
    }

    /**
     * Get the result for this event.
     *
     * @return DocumentInterface
     */
    public function getDocument(): DocumentInterface
    {
        return $this->document;
    }
}
