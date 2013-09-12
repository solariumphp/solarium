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
namespace Solarium\QueryType\Select\Query\Component\Highlighting;

use Solarium\Core\Configurable;

/**
 * Highlighting per-field settings
 *
 * @link http://wiki.apache.org/solr/HighlightingParameters
 */
class Field extends Configurable
{
    /**
     * Value for fragmenter option gap
     */
    const FRAGMENTER_GAP = 'gap';

    /**
     * Value for fragmenter option regex
     */
    const FRAGMENTER_REGEX = 'regex';

    /**
     * Get name option
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getOption('name');
    }

    /**
     * Set name option
     *
     * @param  string $name
     * @return self   Provides fluent interface
     */
    public function setName($name)
    {
        return $this->setOption('name', $name);
    }

    /**
     * Set snippets option
     *
     * Maximum number of snippets per field
     *
     * @param  int  $maximum
     * @return self Provides fluent interface
     */
    public function setSnippets($maximum)
    {
        return $this->setOption('snippets', $maximum);
    }

    /**
     * Get snippets option
     *
     * @return int|null
     */
    public function getSnippets()
    {
        return $this->getOption('snippets');
    }

    /**
     * Set fragsize option
     *
     * The size, in characters, of fragments to consider for highlighting
     *
     * @param  int  $size
     * @return self Provides fluent interface
     */
    public function setFragSize($size)
    {
        return $this->setOption('fragsize', $size);
    }

    /**
     * Get fragsize option
     *
     * @return int|null
     */
    public function getFragSize()
    {
        return $this->getOption('fragsize');
    }

    /**
     * Set mergeContiguous option
     *
     * Collapse contiguous fragments into a single fragment
     *
     * @param  boolean $merge
     * @return self    Provides fluent interface
     */
    public function setMergeContiguous($merge)
    {
        return $this->setOption('mergecontiguous', $merge);
    }

    /**
     * Get mergeContiguous option
     *
     * @return boolean|null
     */
    public function getMergeContiguous()
    {
        return $this->getOption('mergecontiguous');
    }

    /**
     * Set alternatefield option
     *
     * @param  string $field
     * @return self   Provides fluent interface
     */
    public function setAlternateField($field)
    {
        return $this->setOption('alternatefield', $field);
    }

    /**
     * Get alternatefield option
     *
     * @return string|null
     */
    public function getAlternateField()
    {
        return $this->getOption('alternatefield');
    }

    /**
     * Set formatter option
     *
     * @param  string $formatter
     * @return self   Provides fluent interface
     */
    public function setFormatter($formatter = 'simple')
    {
        return $this->setOption('formatter', $formatter);
    }

    /**
     * Get formatter option
     *
     * @return string|null
     */
    public function getFormatter()
    {
        return $this->getOption('formatter');
    }

    /**
     * Set simple prefix option
     *
     * Solr option h1.simple.pre
     *
     * @param  string $prefix
     * @return self   Provides fluent interface
     */
    public function setSimplePrefix($prefix)
    {
        return $this->setOption('simpleprefix', $prefix);
    }

    /**
     * Get simple prefix option
     *
     * Solr option hl.simple.pre
     *
     * @return string|null
     */
    public function getSimplePrefix()
    {
        return $this->getOption('simpleprefix');
    }

    /**
     * Set simple postfix option
     *
     * Solr option h1.simple.post
     *
     * @param  string $postfix
     * @return self   Provides fluent interface
     */
    public function setSimplePostfix($postfix)
    {
        return $this->setOption('simplepostfix', $postfix);
    }

    /**
     * Get simple postfix option
     *
     * Solr option hl.simple.post
     *
     * @return string|null
     */
    public function getSimplePostfix()
    {
        return $this->getOption('simplepostfix');
    }

    /**
     * Set fragmenter option
     *
     * Use one of the constants as value.
     *
     * @param  string $fragmenter
     * @return self   Provides fluent interface
     */
    public function setFragmenter($fragmenter)
    {
        return $this->setOption('fragmenter', $fragmenter);
    }

    /**
     * Get fragmenter option
     *
     * @return string|null
     */
    public function getFragmenter()
    {
        return $this->getOption('fragmenter');
    }

    /**
     * Set useFastVectorHighlighter option
     *
     * @param  boolean $use
     * @return self    Provides fluent interface
     */
    public function setUseFastVectorHighlighter($use)
    {
        return $this->setOption('usefastvectorhighlighter', $use);
    }

    /**
     * Get useFastVectorHighlighter option
     *
     * @return boolean|null
     */
    public function getUseFastVectorHighlighter()
    {
        return $this->getOption('usefastvectorhighlighter');
    }
}
