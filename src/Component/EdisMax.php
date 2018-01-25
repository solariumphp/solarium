<?php

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\EdisMax as RequestBuilder;

/**
 * EdisMax component.
 *
 * @see http://wiki.apache.org/solr/ExtendedDisMax
 */
class EdisMax extends DisMax
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'queryparser' => 'edismax',
    ];

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType()
    {
        return ComponentAwareQueryInterface::COMPONENT_EDISMAX;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Set BoostFunctionsMult option.
     *
     * Functions (with optional boosts) that will be included in the
     * user's query to influence the score by multiplying its value.
     *
     * Format is: "funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2"
     *
     * @param string $boostFunctionsMult
     *
     * @return self Provides fluent interface
     */
    public function setBoostFunctionsMult($boostFunctionsMult)
    {
        return $this->setOption('boostfunctionsmult', $boostFunctionsMult);
    }

    /**
     * Get BoostFunctionsMult option.
     *
     * @return string|null
     */
    public function getBoostFunctionsMult()
    {
        return $this->getOption('boostfunctionsmult');
    }

    /**
     * Set PhraseFields option.
     *
     * As with 'pf' but chops the input into bi-grams,
     * e.g. "the brown fox jumped" is queried as "the brown" "brown fox" "fox jumped"
     *
     * Format is: "fieldA^1.0 fieldB^2.2 fieldC^3.5"
     *
     * @param string $phraseBigramFields
     *
     * @return self Provides fluent interface
     */
    public function setPhraseBigramFields($phraseBigramFields)
    {
        return $this->setOption('phrasebigramfields', $phraseBigramFields);
    }

    /**
     * Get PhraseBigramFields option.
     *
     * @return string|null
     */
    public function getPhraseBigramFields()
    {
        return $this->getOption('phrasebigramfields');
    }

    /**
     * Set PhraseBigramSlop option.
     *
     * As with 'ps' but sets default slop factor for 'pf2'.
     * If not specified, 'ps' will be used.
     *
     * @param string $phraseBigramSlop
     *
     * @return self Provides fluent interface
     */
    public function setPhraseBigramSlop($phraseBigramSlop)
    {
        return $this->setOption('phrasebigramslop', $phraseBigramSlop);
    }

    /**
     * Get PhraseBigramSlop option.
     *
     * @return string|null
     */
    public function getPhraseBigramSlop()
    {
        return $this->getOption('phrasebigramslop');
    }

    /**
     * Set PhraseFields option.
     *
     * As with 'pf' but chops the input into tri-grams,
     * e.g. "the brown fox jumped" is queried as "the brown fox" "brown fox jumped"
     *
     * Format is: "fieldA^1.0 fieldB^2.2 fieldC^3.5"
     *
     * @param string $phraseTrigramFields
     *
     * @return self Provides fluent interface
     */
    public function setPhraseTrigramFields($phraseTrigramFields)
    {
        return $this->setOption('phrasetrigramfields', $phraseTrigramFields);
    }

    /**
     * Get PhraseTrigramFields option.
     *
     * @return string|null
     */
    public function getPhraseTrigramFields()
    {
        return $this->getOption('phrasetrigramfields');
    }

    /**
     * Set PhraseTrigramSlop option.
     *
     * As with 'ps' but sets default slop factor for 'pf3'.
     * If not specified, 'ps' will be used.
     *
     * @param string $phraseTrigramSlop
     *
     * @return self Provides fluent interface
     */
    public function setPhraseTrigramSlop($phraseTrigramSlop)
    {
        return $this->setOption('phrasetrigramslop', $phraseTrigramSlop);
    }

    /**
     * Get PhraseTrigramSlop option.
     *
     * @return string|null
     */
    public function getPhraseTrigramSlop()
    {
        return $this->getOption('phrasetrigramslop');
    }

    /**
     * Set UserFields option.
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
     *
     * @return self Provides fluent interface
     */
    public function setUserFields($userFields)
    {
        return $this->setOption('userfields', $userFields);
    }

    /**
     * Get UserFields option.
     *
     * @return string|null
     */
    public function getUserFields()
    {
        return $this->getOption('userfields');
    }
}
