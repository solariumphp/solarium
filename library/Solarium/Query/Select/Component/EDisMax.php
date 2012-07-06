<?php
/**
 * Copyright 2012 Marc Morera. All rights reserved.
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
 * @copyright Copyright 2012 Marc Morera <yuhu@mmoreram.com>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Query
 */

/**
 * EDisMax component
 *
 * @link http://wiki.apache.org/solr/ExtendedDisMax
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Select_Component_EDisMax extends Solarium_Query_Select_Component_DisMax
{

    /**
     * Component type
     *
     * @var string
     */
    protected $_type = Solarium_Query_Select::COMPONENT_EDISMAX;

    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
        'queryparser' => 'edismax',
    );

    /**
     * Set BoostFunctionsMult option
     *
     * Functions (with optional boosts) that will be included in the
     * user's query to influence the score by multiplying its value.
     *
     * Format is: "funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2"
     *
     * @param string $boostFunctionsMult
     * @return Solarium_Query_Select_Component_EDisMax Provides fluent interface
     */
    public function setBoostFunctionsMult($boostFunctionsMult)
    {
        return $this->_setOption('boostfunctionsmult', $boostFunctionsMult);
    }

    /**
     * Get BoostFunctionsMult option
     *
     * @return string|null
     */
    public function getBoostFunctionsMult()
    {
        return $this->getOption('boostfunctionsmult');
    }

    /**
     * Set PhraseFields option
     *
     * As with 'pf' but chops the input into bi-grams,
     * e.g. "the brown fox jumped" is queried as "the brown" "brown fox" "fox jumped"
     *
     * Format is: "fieldA^1.0 fieldB^2.2 fieldC^3.5"
     *
     * @param string $phraseBigramFields
     * @return Solarium_Query_Select_Component_EDisMax Provides fluent interface
     */
    public function setPhraseBigramFields($phraseBigramFields)
    {
        return $this->_setOption('phrasebigramfields', $phraseBigramFields);
    }

    /**
     * Get PhraseBigramFields option
     *
     * @return string|null
     */
    public function getPhraseBigramFields()
    {
        return $this->getOption('phrasebigramfields');
    }

    /**
     * Set PhraseBigramSlop option
     *
     * As with 'ps' but sets default slop factor for 'pf2'.
     * If not specified, 'ps' will be used.
     *
     * @param string $phraseBigramSlop
     * @return Solarium_Query_Select_Component_EDisMax Provides fluent interface
     */
    public function setPhraseBigramSlop($phraseBigramSlop)
    {
        return $this->_setOption('phrasebigramslop', $phraseBigramSlop);
    }

    /**
     * Get PhraseBigramSlop option
     *
     * @return string|null
     */
    public function getPhraseBigramSlop()
    {
        return $this->getOption('phrasebigramslop');
    }

    /**
     * Set PhraseFields option
     *
     * As with 'pf' but chops the input into tri-grams,
     * e.g. "the brown fox jumped" is queried as "the brown fox" "brown fox jumped"
     *
     * Format is: "fieldA^1.0 fieldB^2.2 fieldC^3.5"
     *
     * @param string $phraseTrigramFields
     * @return Solarium_Query_Select_Component_EDisMax Provides fluent interface
     */
    public function setPhraseTrigramFields($phraseTrigramFields)
    {
        return $this->_setOption('phrasetrigramfields', $phraseTrigramFields);
    }

    /**
     * Get PhraseTrigramFields option
     *
     * @return string|null
     */
    public function getPhraseTrigramFields()
    {
        return $this->getOption('phrasetrigramfields');
    }

    /**
     * Set PhraseTrigramSlop option
     *
     * As with 'ps' but sets default slop factor for 'pf3'.
     * If not specified, 'ps' will be used.
     *
     * @param string $phraseTrigramSlop
     * @return Solarium_Query_Select_Component_EDisMax Provides fluent interface
     */
    public function setPhraseTrigramSlop($phraseTrigramSlop)
    {
        return $this->_setOption('phrasetrigramslop', $phraseTrigramSlop);
    }

    /**
     * Get PhraseTrigramSlop option
     *
     * @return string|null
     */
    public function getPhraseTrigramSlop()
    {
        return $this->getOption('phrasetrigramslop');
    }

    /**
     * Set UserFields option
     *
     * Specifies which schema fields the end user shall be allowed to query for explicitly.
     * This parameter supports wildcards.
     *
     * The default is to allow all fields, equivalent to &uf=*.
     * To allow only title field, use &uf=title, to allow title and all fields ending with _s, use &uf=title *_s.
     * To allow all fields except title, use &uf=* -title. To disallow all fielded searches, use &uf=-*.
     * The uf parameter was introduced in Solr3.6
     *
     * @param string $userFields
     * @return Solarium_Query_Select_Component_EDisMax Provides fluent interface
     */
    public function setUserFields($userFields)
    {
        return $this->_setOption('userfields', $userFields);
    }


    /**
     * Get UserFields option
     *
     * @return string|null
     */
    public function getUserFields()
    {
        return $this->getOption('userfields');
    }

}