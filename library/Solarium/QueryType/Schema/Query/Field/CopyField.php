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
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Schema\Query\Field;

/**
 * Class CopyField
 * @author Beno!t POLASZEK
 */
class CopyField
{
    protected $source = '';

    protected $dest = array();

    protected $maxChars;

    public function __construct($source = null, $dest = null, $maxChars = null)
    {
        if (is_null($source)) {
            $this->setSource($source);
        }
        if (is_null($dest)) {
            $this->setDest($dest);
        }
        if (is_null($maxChars)) {
            $this->setMaxChars($maxChars);
        }
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return $this - Provides Fluent Interface
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return array
     */
    public function getDest()
    {
        return $this->dest;
    }

    /**
     * @param array $dest
     * @return $this - Provides Fluent Interface
     */
    public function setDest($dest)
    {
        if (is_array($dest)) {
            $this->dest = array_map(
                function($field) {
                    return (string) $field;
                },
                $dest
            );
        } else {
            $this->dest = array((string) $dest);
        }

        return $this;
    }

    /**
     * @param $dest
     * @return $this
     */
    public function addDest($dest)
    {
        if (!$this->dest) {
            $this->dest = $dest;
        } elseif (is_array($this->dest)) {
            $this->dest[] = $dest;
        } else {
            $this->dest = array($this->dest, $dest);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxChars()
    {
        return $this->maxChars;
    }

    /**
     * @param mixed $maxChars
     * @return $this - Provides Fluent Interface
     */
    public function setMaxChars($maxChars)
    {
        $this->maxChars = (!is_null($maxChars)) ? (int) $maxChars : null;

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function castAsArray()
    {
        $output = array(
            'source' => $this->getSource(),
            'dest' => $this->getDest(),
        );
        if (!is_null($this->getMaxChars())) {
            $output['maxChars'] = $this->getMaxChars();
        }

        return $output;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSource();
    }

}
