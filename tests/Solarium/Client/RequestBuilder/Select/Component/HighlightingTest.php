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
 */

class Solarium_Client_RequestBuilder_Select_Component_HighlightingTest extends PHPUnit_Framework_TestCase
{

    public function testBuildComponent()
    {
        $builder = new Solarium_Client_RequestBuilder_Select_Component_Highlighting;
        $request = new Solarium_Client_Request();

        $component = new Solarium_Query_Select_Component_Highlighting();
        $component->addField('fieldA');

        $field = $component->getField('fieldB');
        $field->setSnippets(3);
        $field->setFragSize(25);
        $field->setMergeContiguous(true);
        $field->setAlternateField('text');
        $field->setFormatter('myFormatter');
        $field->setSimplePrefix('<b>');
        $field->setSimplePostfix('</b>');
        $field->setFragmenter('myFragmenter');
        $field->setUseFastVectorHighlighter(true);

        $component->setSnippets(2);
        $component->setFragSize(3);
        $component->setMergeContiguous(true);
        $component->setRequireFieldMatch(false);
        $component->setMaxAnalyzedChars(4);
        $component->setAlternateField('fieldC');
        $component->setMaxAlternateFieldLength(5);
        $component->setFormatter('simple');
        $component->setSimplePrefix('<b>');
        $component->setSimplePostfix('</b>');
        $component->setFragmenter('myFragmenter');
        $component->setFragListBuilder('myFragListBuilder');
        $component->setFragmentsBuilder('myFragmentsBuilder');
        $component->setUsePhraseHighlighter(true);
        $component->setUseFastVectorHighlighter(false);
        $component->setHighlightMultiTerm(true);
        $component->setRegexSlop(1.3);
        $component->setRegexPattern('mypattern');
        $component->setMaxAnalyzedChars(100);
        $component->setQuery('text:myvalue');
        $component->setPhraseLimit(40);

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            array(
                'hl' => 'true',
                'hl.fl' => 'fieldA,fieldB',
                'hl.snippets' => 2,
                'hl.fragsize' => 3,
                'hl.mergeContiguous' => 'true',
                'hl.requireFieldMatch' => 'false',
                'hl.maxAnalyzedChars' => 100,
                'hl.alternateField' => 'fieldC',
                'hl.maxAlternateFieldLength' => 5,
                'hl.formatter' => 'simple',
                'hl.simple.pre' => '<b>',
                'hl.simple.post' => '</b>',
                'hl.fragmenter' => 'myFragmenter',
                'hl.fragListBuilder' => 'myFragListBuilder',
                'hl.fragmentsBuilder' => 'myFragmentsBuilder',
                'hl.useFastVectorHighlighter' => 'false',
                'hl.usePhraseHighlighter' => 'true',
                'hl.highlightMultiTerm' => 'true',
                'hl.regex.slop' => 1.3,
                'hl.regex.pattern' => 'mypattern',
                'hl.q' => 'text:myvalue',
                'hl.phraseLimit' => 40,
                'f.fieldB.hl.snippets' => 3,
                'f.fieldB.hl.fragsize' => 25,
                'f.fieldB.hl.mergeContiguous' => 'true',
                'f.fieldB.hl.alternateField' => 'text',
                'f.fieldB.hl.formatter' => 'myFormatter',
                'f.fieldB.hl.simple.pre' => '<b>',
                'f.fieldB.hl.simple.post' => '</b>',
                'f.fieldB.hl.fragmenter' => 'myFragmenter',
                'f.fieldB.hl.useFastVectorHighlighter' => 'true',
            ),
            $request->getParams()
        );

    }

}
