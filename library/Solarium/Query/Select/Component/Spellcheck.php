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
 *
 * @package Solarium
 * @subpackage Query
 */

/**
 * Spellcheck component
 *
 * @link http://wiki.apache.org/solr/SpellCheckComponent
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Select_Component_Spellcheck extends Solarium_Query_Select_Component
{
    /**
     * Component type
     *
     * @var string
     */
    protected $_type = Solarium_Query_Select::COMPONENT_SPELLCHECK;

    /**
     * Set query option
     *
     * Query to spellcheck
     *
     * @param string $query
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setQuery($query)
    {
        return $this->_setOption('query', $query);
    }

    /**
     * Get query option
     *
     * @return string|null
     */
    public function getQuery()
    {
        return $this->getOption('query');
    }

    /**
     * Set build option
     *
     * Build the spellcheck?
     *
     * @param boolean $build
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setBuild($build)
    {
        return $this->_setOption('build', $build);
    }

    /**
     * Get build option
     *
     * @return boolean|null
     */
    public function getBuild()
    {
        return $this->getOption('build');
    }

    /**
     * Set reload option
     *
     * Reload the dictionary?
     *
     * @param boolean $reload
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setReload($reload)
    {
        return $this->_setOption('reload', $reload);
    }

    /**
     * Get fragsize option
     *
     * @return boolean|null
     */
    public function getReload()
    {
        return $this->getOption('reload');
    }

    /**
     * Set dictionary option
     *
     * The name of the dictionary to use
     *
     * @param string $dictionary
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setDictionary($dictionary)
    {
        return $this->_setOption('dictionary', $dictionary);
    }

    /**
     * Get dictionary option
     *
     * @return string|null
     */
    public function getDictionary()
    {
        return $this->getOption('dictionary');
    }

    /**
     * Set count option
     *
	 * The maximum number of suggestions to return
	 *
     * @param int $count
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setCount($count)
    {
        return $this->_setOption('count', $count);
    }

    /**
     * Get count option
     *
     * @return int|null
     */
    public function getCount()
    {
        return $this->getOption('count');
    }

    /**
     * Set onlyMorePopular option
     *
     * Only return suggestions that result in more hits for the query than the existing query
     *
     * @param boolean $onlyMorePopular
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setOnlyMorePopular($onlyMorePopular)
    {
        return $this->_setOption('onlymorepopular', $onlyMorePopular);
    }

    /**
     * Get onlyMorePopular option
     *
     * @return boolean|null
     */
    public function getOnlyMorePopular()
    {
        return $this->getOption('onlymorepopular');
    }

    /**
     * Set extendedResults option
     *
     * @param boolean $extendedResults
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setExtendedResults($extendedResults)
    {
        return $this->_setOption('extendedresults', $extendedResults);
    }

    /**
     * Get extendedResults option
     *
     * @return boolean|null
     */
    public function getExtendedResults()
    {
        return $this->getOption('extendedresults');
    }

    /**
     * Set collate option
     *
     * @param boolean $collate
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setCollate($collate)
    {
        return $this->_setOption('collate', $collate);
    }

    /**
     * Get collate option
     *
     * @return boolean|null
     */
    public function getCollate()
    {
        return $this->getOption('collate');
    }

    /**
     * Set maxCollations option
     *
     * @param int $maxCollations
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setMaxCollations($maxCollations)
    {
        return $this->_setOption('maxcollations', $maxCollations);
    }

    /**
     * Get maxCollations option
     *
     * @return int|null
     */
    public function getMaxCollations()
    {
        return $this->getOption('maxcollations');
    }

    /**
     * Set maxCollationTries option
     *
     * @param string $maxCollationTries
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setMaxCollationTries($maxCollationTries)
    {
        return $this->_setOption('maxcollationtries', $maxCollationTries);
    }

    /**
     * Get maxCollationTries option
     *
     * @return string|null
     */
    public function getMaxCollationTries()
    {
        return $this->getOption('maxcollationtries');
    }

    /**
     * Set maxCollationEvaluations option
     *
     * @param int $maxCollationEvaluations
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setMaxCollationEvaluations($maxCollationEvaluations)
    {
        return $this->_setOption('maxcollationevaluations', $maxCollationEvaluations);
    }

    /**
     * Get maxCollationEvaluations option
     *
     * @return int|null
     */
    public function getMaxCollationEvaluations()
    {
        return $this->getOption('maxcollationevaluations');
    }

    /**
     * Set collateExtendedResults option
     *
     * @param string $collateExtendedResults
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setCollateExtendedResults($collateExtendedResults)
    {
        return $this->_setOption('collateextendedresults', $collateExtendedResults);
    }

    /**
     * Get collateExtendedResults option
     *
     * @return string|null
     */
    public function getCollateExtendedResults()
    {
        return $this->getOption('collateextendedresults');
    }

    /**
     * Set accuracy option
     *
     * @param float $accuracy
     * @return Solarium_Query_Select_Component_Spellcheck Provides fluent interface
     */
    public function setAccuracy($accuracy)
    {
        return $this->_setOption('accuracy', $accuracy);
    }

    /**
     * Get accuracy option
     *
     * @return float|null
     */
    public function getAccuracy()
    {
        return $this->getOption('accuracy');
    }

}