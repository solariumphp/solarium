<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\EdisMax as RequestBuilder;

/**
 * EdisMax component.
 *
 * @see https://solr.apache.org/guide/the-extended-dismax-query-parser.html
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
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_EDISMAX;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
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
    public function setBoostFunctionsMult(string $boostFunctionsMult): self
    {
        $this->setOption('boostfunctionsmult', $boostFunctionsMult);

        return $this;
    }

    /**
     * Get BoostFunctionsMult option.
     *
     * @return string|null
     */
    public function getBoostFunctionsMult(): ?string
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
    public function setPhraseBigramFields(string $phraseBigramFields): self
    {
        $this->setOption('phrasebigramfields', $phraseBigramFields);

        return $this;
    }

    /**
     * Get PhraseBigramFields option.
     *
     * @return string|null
     */
    public function getPhraseBigramFields(): ?string
    {
        return $this->getOption('phrasebigramfields');
    }

    /**
     * Set PhraseBigramSlop option.
     *
     * As with 'ps' but sets default slop factor for 'pf2'.
     * If not specified, 'ps' will be used.
     *
     * @param int $phraseBigramSlop
     *
     * @return self Provides fluent interface
     */
    public function setPhraseBigramSlop(int $phraseBigramSlop): self
    {
        $this->setOption('phrasebigramslop', $phraseBigramSlop);

        return $this;
    }

    /**
     * Get PhraseBigramSlop option.
     *
     * @return int|null
     */
    public function getPhraseBigramSlop(): ?int
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
    public function setPhraseTrigramFields(string $phraseTrigramFields): self
    {
        $this->setOption('phrasetrigramfields', $phraseTrigramFields);

        return $this;
    }

    /**
     * Get PhraseTrigramFields option.
     *
     * @return string|null
     */
    public function getPhraseTrigramFields(): ?string
    {
        return $this->getOption('phrasetrigramfields');
    }

    /**
     * Set PhraseTrigramSlop option.
     *
     * As with 'ps' but sets default slop factor for 'pf3'.
     * If not specified, 'ps' will be used.
     *
     * @param int $phraseTrigramSlop
     *
     * @return self Provides fluent interface
     */
    public function setPhraseTrigramSlop(int $phraseTrigramSlop): self
    {
        $this->setOption('phrasetrigramslop', $phraseTrigramSlop);

        return $this;
    }

    /**
     * Get PhraseTrigramSlop option.
     *
     * @return int|null
     */
    public function getPhraseTrigramSlop(): ?int
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
    public function setUserFields(string $userFields): self
    {
        $this->setOption('userfields', $userFields);

        return $this;
    }

    /**
     * Get UserFields option.
     *
     * @return string|null
     */
    public function getUserFields(): ?string
    {
        return $this->getOption('userfields');
    }
}
