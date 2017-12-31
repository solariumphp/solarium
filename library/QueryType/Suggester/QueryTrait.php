<?php
/**
 * Copyright 2011 Markus Kalkbrenner. All rights reserved.
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

namespace Solarium\QueryType\Suggester;

/**
 * Suggester Query Trait.
 */
trait QueryTrait
{
    /**
     * Get query option.
     *
     * @return string|null
     */
    public function getQuery()
    {
        return $this->getOption('query');
    }

    /**
     * Set dictionary option.
     *
     * The name of the dictionary to use
     *
     * @param string $dictionary
     *
     * @return self Provides fluent interface
     */
    public function setDictionary($dictionary)
    {
        return $this->setOption('dictionary', $dictionary);
    }

    /**
     * Get dictionary option.
     *
     * @return string|null
     */
    public function getDictionary()
    {
        return $this->getOption('dictionary');
    }

    /**
     * Set count option.
     *
     * The maximum number of suggestions to return
     *
     * @param int $count
     *
     * @return self Provides fluent interface
     */
    public function setCount($count)
    {
        return $this->setOption('count', $count);
    }

    /**
     * Get count option.
     *
     * @return int|null
     */
    public function getCount()
    {
        return $this->getOption('count');
    }

    /**
     * Set cfq option.
     *
     * A Context Filter Query used to filter suggestions based on the context field, if supported by the suggester.
     *
     * @param string $cfq
     *
     * @return self Provides fluent interface
     */
    public function setContextFilterQuery($cfq)
    {
        return $this->setOption('cfq', $cfq);
    }

    /**
     * Get cfq option.
     *
     * @return string|null
     */
    public function getContextFilterQuery()
    {
        return $this->getOption('cfq');
    }

    /**
     * Set build option.
     *
     * @param boolean $build
     *
     * @return self Provides fluent interface
     */
    public function setBuild($build)
    {
        return $this->setOption('build', $build);
    }

    /**
     * Get build option.
     *
     * @return boolean|null
     */
    public function getBuild()
    {
        return $this->getOption('build');
    }

    /**
     * Set reload option.
     *
     * @param boolean $build
     *
     * @return self Provides fluent interface
     */
    public function setReload($reload)
    {
      return $this->setOption('reload', $reload);
    }

    /**
     * Get reload option.
     *
     * @return boolean|null
     */
    public function getReload()
    {
      return $this->getOption('reload');
    }
}
