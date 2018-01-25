<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Highlighting\Highlighting as Component;
use Solarium\Component\RequestBuilder\Highlighting as RequestBuilder;
use Solarium\Core\Client\Request;

class HighlightingTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->addField('fieldA');

        $field = $component->getField('fieldB');
        $field->setSnippets(3);
        $field->setFragSize(25);
        $field->setMergeContiguous(true);
        $field->setAlternateField('text');
        $field->setPreserveMulti(true);
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
        $component->setPreserveMulti(true);
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
        $component->setTagPrefix('<i>');
        $component->setTagPostfix('</i>');
        $component->setMultiValuedSeparatorChar('|');
        $component->setBoundaryScannerChars('.,');
        $component->setBoundaryScannerMaxScan(16);
        $component->setBoundaryScannerType($component::BOUNDARYSCANNER_TYPE_WORD);
        $component->setBoundaryScannerCountry('be');
        $component->setBoundaryScannerLanguage('en');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            [
                'hl' => 'true',
                'hl.fl' => 'fieldA,fieldB',
                'hl.snippets' => 2,
                'hl.fragsize' => 3,
                'hl.mergeContiguous' => 'true',
                'hl.requireFieldMatch' => 'false',
                'hl.maxAnalyzedChars' => 100,
                'hl.alternateField' => 'fieldC',
                'hl.maxAlternateFieldLength' => 5,
                'hl.preserveMulti' => 'true',
                'hl.formatter' => 'simple',
                'hl.simple.pre' => '<b>',
                'hl.simple.post' => '</b>',
                'hl.tag.pre' => '<i>',
                'hl.tag.post' => '</i>',
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
                'hl.multiValuedSeparatorChar' => '|',
                'f.fieldB.hl.snippets' => 3,
                'f.fieldB.hl.fragsize' => 25,
                'f.fieldB.hl.mergeContiguous' => 'true',
                'f.fieldB.hl.alternateField' => 'text',
                'f.fieldB.hl.preserveMulti' => 'true',
                'f.fieldB.hl.formatter' => 'myFormatter',
                'f.fieldB.hl.simple.pre' => '<b>',
                'f.fieldB.hl.simple.post' => '</b>',
                'f.fieldB.hl.fragmenter' => 'myFragmenter',
                'f.fieldB.hl.useFastVectorHighlighter' => 'true',
                'hl.bs.maxScan' => 16,
                'hl.bs.chars' => '.,',
                'hl.bs.type' => 'WORD',
                'hl.bs.country' => 'be',
                'hl.bs.language' => 'en',
            ],
            $request->getParams()
        );
    }

    public function testBuildComponentWithoutFields()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setSnippets(2);
        $component->setFragSize(3);
        $component->setMergeContiguous(true);
        $component->setRequireFieldMatch(false);
        $component->setMaxAnalyzedChars(4);
        $component->setAlternateField('fieldC');
        $component->setMaxAlternateFieldLength(5);
        $component->setPreserveMulti(true);
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
        $component->setTagPrefix('<i>');
        $component->setTagPostfix('</i>');
        $component->setMultiValuedSeparatorChar('|');
        $component->setBoundaryScannerChars('.,');
        $component->setBoundaryScannerMaxScan(16);
        $component->setBoundaryScannerType($component::BOUNDARYSCANNER_TYPE_WORD);
        $component->setBoundaryScannerCountry('be');
        $component->setBoundaryScannerLanguage('en');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
                [
                    'hl' => 'true',
                    'hl.snippets' => 2,
                    'hl.fragsize' => 3,
                    'hl.mergeContiguous' => 'true',
                    'hl.requireFieldMatch' => 'false',
                    'hl.maxAnalyzedChars' => 100,
                    'hl.alternateField' => 'fieldC',
                    'hl.maxAlternateFieldLength' => 5,
                    'hl.preserveMulti' => 'true',
                    'hl.formatter' => 'simple',
                    'hl.simple.pre' => '<b>',
                    'hl.simple.post' => '</b>',
                    'hl.tag.pre' => '<i>',
                    'hl.tag.post' => '</i>',
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
                    'hl.multiValuedSeparatorChar' => '|',
                    'hl.bs.maxScan' => 16,
                    'hl.bs.chars' => '.,',
                    'hl.bs.type' => 'WORD',
                    'hl.bs.country' => 'be',
                    'hl.bs.language' => 'en',
                ],
                $request->getParams()
            );
    }
}
