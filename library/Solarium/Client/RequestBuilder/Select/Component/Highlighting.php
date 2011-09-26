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
 * @subpackage Client
 */

/**
 * Add select component Highlighting to the request
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_RequestBuilder_Select_Component_Highlighting
{
    
    /**
     * Add request settings for Highlighting
     *
     * @param Solarium_Query_Select_Component_Highlighting $component
     * @param Solarium_Client_Request $request
     * @return Solarium_Client_Request
     */
    public function build(Solarium_Query_Select_Component_Highlighting $component, Solarium_Client_Request $request)
    {
        // enable highlighting
        $request->addParam('hl', 'true');

        $request->addParam('hl.fl', $component->getFields());
        $request->addParam('hl.snippets', $component->getSnippets());
        $request->addParam('hl.fragsize', $component->getFragSize());
        $request->addParam('hl.mergeContiguous', $component->getMergeContiguous());
        $request->addParam('hl.requireFieldMatch', $component->getRequireFieldMatch());
        $request->addParam('hl.maxAnalyzedChars', $component->getMaxAnalyzedChars());
        $request->addParam('hl.alternateField', $component->getAlternateField());
        $request->addParam('hl.maxAlternateFieldLength', $component->getMaxAlternateFieldLength());
        $request->addParam('hl.formatter', $component->getFormatter());
        $request->addParam('hl.simple.pre', $component->getSimplePrefix());
        $request->addParam('hl.simple.post', $component->getSimplePostfix());
        $request->addParam('hl.fragmenter', $component->getFragmenter());
        $request->addParam('hl.fragListBuilder', $component->getFragListBuilder());
        $request->addParam('hl.fragmentsBuilder', $component->getFragmentsBuilder());
        $request->addParam('hl.useFastVectorHighlighter', $component->getUseFastVectorHighlighter());
        $request->addParam('hl.usePhraseHighlighter', $component->getUsePhraseHighlighter());
        $request->addParam('hl.highlightMultiTerm', $component->getHighlightMultiTerm());
        $request->addParam('hl.regex.slop', $component->getRegexSlop());
        $request->addParam('hl.regex.pattern', $component->getRegexPattern());
        $request->addParam('hl.regex.maxAnalyzedChars', $component->getRegexMaxAnalyzedChars());
        
        $fieldOptions = $component->getPerFieldOptions();
        if (sizeof($fieldOptions)) {
            $this->_buildPerField($fieldOptions, $request);
        }

        return $request;
    }
    
    /**
     * Set the per field highlighting options
     * 
     * @param array $fieldOptions
     * @param Solarium_Client_Request $request 
     */
    protected function _buildPerField(array $fieldOptions, Solarium_Client_Request $request)
    {
        foreach ($fieldOptions as $field => $options) {
            foreach ($options as $option => $value) {
                $request->addParam('f.' . $field . '.hl.' . $option, $value, true);
            }
        }
    }
}