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

namespace Solarium\Plugin\MinimumScoreFilter;

use Solarium\QueryType\Select\Result\DocumentInterface;
use Solarium\Exception\RuntimeException;

/**
 * Minimum score filter query result document.
 *
 * Decorates the original document with a filter indicator
 */
class Document implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * Original document.
     *
     * @var array
     */
    protected $document;

    /**
     * Is this document marked as a low score?
     *
     * @var boolean
     */
    protected $marked;

    /**
     * Constructor.
     *
     * @param DocumentInterface $document
     * @param int               $threshold
     */
    public function __construct(DocumentInterface $document, $threshold)
    {
        $this->document = $document;
        $this->marked = $threshold > $document->score;
    }

    /**
     * Get markedAsLowScore status.
     *
     * @return bool
     */
    public function markedAsLowScore()
    {
        return $this->marked;
    }

    /**
     * Forward all other calls to the original document.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->document->$name($arguments);
    }

    /**
     * Forward all other calls to the original document.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->document->__get($name);
    }

    /**
     * Forward isset call to the original document.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->document->__isset($name);
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->document->getIterator();
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return $this->document->count();
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->document->offsetExists($offset);
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->document->offsetUnset($offset);
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->document->offsetGet($offset);
    }

    /**
     * Set field value.
     *
     * Magic method for setting a field as property of this object. Since this
     * is a readonly document an exception will be thrown to prevent this.
     *
     * @throws RuntimeException
     *
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        throw new RuntimeException('A readonly document cannot be altered');
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }
}
