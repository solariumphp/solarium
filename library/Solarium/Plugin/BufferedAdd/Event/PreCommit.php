<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\Plugin\BufferedAdd\Event;

use Symfony\Component\EventDispatcher\Event;
use Solarium\QueryType\Select\Result\DocumentInterface;

/**
 * PreCommit event, see Events for details.
 */
class PreCommit extends Event
{
    /**
     * @var DocumentInterface[]
     */
    protected $buffer;

    /**
     * @var boolean
     */
    protected $overwrite;

    /**
     * @var boolean
     */
    protected $softCommit;

    /**
     * @var boolean
     */
    protected $waitSearcher;

    /**
     * @var boolean
     */
    protected $expungeDeletes;

    /**
     * Event constructor.
     *
     * @param array   $buffer
     * @param boolean $overwrite
     * @param boolean $softCommit
     * @param boolean $waitSearcher
     * @param boolean $expungeDeletes
     */
    public function __construct($buffer, $overwrite, $softCommit, $waitSearcher, $expungeDeletes)
    {
        $this->buffer = $buffer;
        $this->overwrite = $overwrite;
        $this->softCommit = $softCommit;
        $this->waitSearcher = $waitSearcher;
        $this->expungeDeletes = $expungeDeletes;
    }

    /**
     * Get the buffer for this event.
     *
     * @return DocumentInterface[]
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * Set the buffer for this event, this way you can alter the buffer before it is committed to Solr.
     *
     * @param array $buffer
     */
    public function setBuffer($buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * Optionally override the value.
     *
     * @param boolean $expungeDeletes
     */
    public function setExpungeDeletes($expungeDeletes)
    {
        $this->expungeDeletes = $expungeDeletes;
    }

    /**
     * @return boolean
     */
    public function getExpungeDeletes()
    {
        return $this->expungeDeletes;
    }

    /**
     * Optionally override the value.
     *
     * @param boolean $overwrite
     */
    public function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
    }

    /**
     * @return boolean
     */
    public function getOverwrite()
    {
        return $this->overwrite;
    }

    /**
     * Optionally override the value.
     *
     * @param boolean $softCommit
     */
    public function setSoftCommit($softCommit)
    {
        $this->softCommit = $softCommit;
    }

    /**
     * @return boolean
     */
    public function getSoftCommit()
    {
        return $this->softCommit;
    }

    /**
     * Optionally override the value.
     *
     * @param boolean $waitSearcher
     */
    public function setWaitSearcher($waitSearcher)
    {
        $this->waitSearcher = $waitSearcher;
    }

    /**
     * @return boolean
     */
    public function getWaitSearcher()
    {
        return $this->waitSearcher;
    }
}
