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

namespace Solarium\QueryType\Select\Result\Spellcheck;

/**
 * Select component spellcheck suggestion result.
 */
class Suggestion
{
    /**
     * Constructor.
     *
     * @param int   $numFound
     * @param int   $startOffset
     * @param int   $endOffset
     * @param int   $originalFrequency
     * @param array $words
     */
    public function __construct($numFound, $startOffset, $endOffset, $originalFrequency, $words)
    {
        $this->numFound = $numFound;
        $this->startOffset = $startOffset;
        $this->endOffset = $endOffset;
        $this->originalFrequency = $originalFrequency;
        $this->words = $words;
    }

    /**
     * Get numFound value.
     *
     * @return int
     */
    public function getNumFound()
    {
        return $this->numFound;
    }

    /**
     * Get startOffset value.
     *
     * @return int
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * Get endOffset value.
     *
     * @return int
     */
    public function getEndOffset()
    {
        return $this->endOffset;
    }

    /**
     * Get originalFrequency value.
     *
     * Only available if CollateExtendedResults was enabled in your query
     *
     * @return int
     */
    public function getOriginalFrequency()
    {
        return $this->originalFrequency;
    }

    /**
     * Get first word.
     *
     * @return string|null
     */
    public function getWord()
    {
        $word = reset($this->words);
        if (isset($word['word'])) {
            return $word['word'];
        } else {
            return $word;
        }
    }

    /**
     * Get all words (and frequencies).
     *
     * @return array
     */
    public function getWords()
    {
        return $this->words;
    }

    /**
     * Get frequency value.
     *
     * Only available if CollateExtendedResults was enabled in your query
     *
     * @return int
     */
    public function getFrequency()
    {
        $word = reset($this->words);
        if (isset($word['freq'])) {
            return $word['freq'];
        } else {
            return;
        }
    }
}
