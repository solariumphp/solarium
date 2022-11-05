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
        $component->setMethod($component::METHOD_FASTVECTOR);
        $component->addField('fieldA');

        $field = $component->getField('fieldB');
        $field->setMethod($component::METHOD_ORIGINAL);
        $field->setUsePhraseHighlighter(false);
        $field->setHighlightMultiTerm(false);
        $field->setSnippets(3);
        $field->setFragSize(25);
        $field->setMergeContiguous(true);
        $field->setAlternateField('text');
        $field->setFormatter($component::FORMATTER_SIMPLE);
        $field->setSimplePrefix('<b>');
        $field->setSimplePostfix('</b>');
        $field->setFragmenter($component::FRAGMENTER_GAP);
        $field->setRegexSlop(1.3);
        $field->setRegexPattern('mypattern');
        $field->setRegexMaxAnalyzedChars(500);
        $field->setPreserveMulti(true);
        $field->setPayloads(false);

        $component->setQuery('text:myvalue');
        $component->setQueryParser('myparser');
        $component->setRequireFieldMatch(false);
        $component->setQueryFieldPattern(['fieldC', 'fieldD']);
        $component->setUsePhraseHighlighter(true);
        $component->setHighlightMultiTerm(true);
        $component->setSnippets(2);
        $component->setFragSize(3);
        $component->setTagPrefix('<i>');
        $component->setTagPostfix('</i>');
        $component->setEncoder($component::ENCODER_HTML);
        $component->setMaxAnalyzedChars(100);
        $component->setAlternateField('fieldC');
        $component->setMaxAlternateFieldLength(5);
        $component->setHighlightAlternate(false);
        $component->setFragListBuilder($component::FRAGLISTBUILDER_SINGLE);
        $component->setFragmentsBuilder($component::FRAGMENTSBUILDER_DEFAULT);
        $component->setBoundaryScanner($component::BOUNDARYSCANNER_BREAKITERATOR);
        $component->setBoundaryScannerType($component::BOUNDARYSCANNER_TYPE_WORD);
        $component->setBoundaryScannerLanguage('en');
        $component->setBoundaryScannerCountry('BE');
        $component->setBoundaryScannerMaxScan(16);
        $component->setBoundaryScannerChars('.,');
        $component->setPhraseLimit(40);
        $component->setMultiValuedSeparatorChar('|');

        $request = $builder->buildComponent($component, $request);

        $this->assertSame(
            [
                'hl' => 'true',
                'hl.method' => 'fastVector',
                'hl.fl' => 'fieldA,fieldB',
                'hl.q' => 'text:myvalue',
                'hl.qparser' => 'myparser',
                'hl.requireFieldMatch' => 'false',
                'hl.queryFieldPattern' => 'fieldC,fieldD',
                'hl.usePhraseHighlighter' => 'true',
                'hl.highlightMultiTerm' => 'true',
                'hl.snippets' => 2,
                'hl.fragsize' => 3,
                'hl.tag.pre' => '<i>',
                'hl.tag.post' => '</i>',
                'hl.encoder' => 'html',
                'hl.maxAnalyzedChars' => 100,
                'hl.bs.language' => 'en',
                'hl.bs.country' => 'BE',
                'hl.bs.type' => 'WORD',
                'hl.alternateField' => 'fieldC',
                'hl.maxAlternateFieldLength' => 5,
                'hl.highlightAlternate' => 'false',
                'hl.fragListBuilder' => 'single',
                'hl.fragmentsBuilder' => 'default',
                'hl.boundaryScanner' => 'breakIterator',
                'hl.bs.maxScan' => 16,
                'hl.bs.chars' => '.,',
                'hl.phraseLimit' => 40,
                'hl.multiValuedSeparatorChar' => '|',
                'f.fieldB.hl.method' => 'original',
                'f.fieldB.hl.usePhraseHighlighter' => 'false',
                'f.fieldB.hl.highlightMultiTerm' => 'false',
                'f.fieldB.hl.snippets' => 3,
                'f.fieldB.hl.fragsize' => 25,
                'f.fieldB.hl.mergeContiguous' => 'true',
                'f.fieldB.hl.alternateField' => 'text',
                'f.fieldB.hl.formatter' => 'simple',
                'f.fieldB.hl.simple.pre' => '<b>',
                'f.fieldB.hl.simple.post' => '</b>',
                'f.fieldB.hl.fragmenter' => 'gap',
                'f.fieldB.hl.regex.slop' => 1.3,
                'f.fieldB.hl.regex.pattern' => 'mypattern',
                'f.fieldB.hl.regex.maxAnalyzedChars' => 500,
                'f.fieldB.hl.preserveMulti' => 'true',
                'f.fieldB.hl.payloads' => 'false',
            ],
            $request->getParams()
        );
    }

    /**
     * @deprecated Since Solr 6.4
     */
    public function testBuildComponentWithUseFastVectorHighlighter()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setUseFastVectorHighlighter(true);
        $component->addField('fieldA');

        $field = $component->getField('fieldB');
        $field->setUseFastVectorHighlighter(false);

        $request = $builder->buildComponent($component, $request);

        $this->assertSame(
            [
                'hl' => 'true',
                'hl.useFastVectorHighlighter' => 'true',
                'hl.fl' => 'fieldA,fieldB',
                'f.fieldB.hl.useFastVectorHighlighter' => 'false',
            ],
            $request->getParams()
        );
    }

    public function testBuildComponentWithUnifiedHighlighter()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setMethod($component::METHOD_UNIFIED);
        $component->setQuery('text:myvalue');
        $component->setQueryParser('myparser');
        $component->setRequireFieldMatch(false);
        $component->setQueryFieldPattern(['fieldC', 'fieldD']);
        $component->setUsePhraseHighlighter(true);
        $component->setHighlightMultiTerm(true);
        $component->setSnippets(2);
        $component->setFragSize(3);
        $component->setTagPrefix('<i>');
        $component->setTagPostfix('</i>');
        $component->setEncoder($component::ENCODER_HTML);
        $component->setMaxAnalyzedChars(100);
        $component->setOffsetSource($component::OFFSETSOURCE_POSTINGS);
        $component->setFragAlignRatio(.42);
        $component->setFragsizeIsMinimum(false);
        $component->setTagEllipsis('~~~');
        $component->setDefaultSummary(true);
        $component->setScoreK1(1.4);
        $component->setScoreB(.33);
        $component->setScorePivot(83);
        $component->setBoundaryScannerLanguage('en');
        $component->setBoundaryScannerCountry('BE');
        $component->setBoundaryScannerVariant('1995');
        $component->setBoundaryScannerType($component::BOUNDARYSCANNER_TYPE_SEPARATOR);
        $component->setBoundaryScannerSeparator('¶');
        $component->setWeightMatches(false);

        $request = $builder->buildComponent($component, $request);

        $this->assertSame(
            [
                'hl' => 'true',
                'hl.method' => 'unified',
                'hl.q' => 'text:myvalue',
                'hl.qparser' => 'myparser',
                'hl.requireFieldMatch' => 'false',
                'hl.queryFieldPattern' => 'fieldC,fieldD',
                'hl.usePhraseHighlighter' => 'true',
                'hl.highlightMultiTerm' => 'true',
                'hl.snippets' => 2,
                'hl.fragsize' => 3,
                'hl.tag.pre' => '<i>',
                'hl.tag.post' => '</i>',
                'hl.encoder' => 'html',
                'hl.maxAnalyzedChars' => 100,
                'hl.offsetSource' => 'POSTINGS',
                'hl.fragAlignRatio' => .42,
                'hl.fragsizeIsMinimum' => 'false',
                'hl.tag.ellipsis' => '~~~',
                'hl.defaultSummary' => 'true',
                'hl.score.k1' => 1.4,
                'hl.score.b' => .33,
                'hl.score.pivot' => 83,
                'hl.bs.language' => 'en',
                'hl.bs.country' => 'BE',
                'hl.bs.variant' => '1995',
                'hl.bs.type' => 'SEPARATOR',
                'hl.bs.separator' => '¶',
                'hl.weightMatches' => 'false',
            ],
            $request->getParams()
        );
    }

    public function testBuildComponentWithOriginalHighlighter()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setMethod($component::METHOD_ORIGINAL);
        $component->setQuery('text:myvalue');
        $component->setQueryParser('myparser');
        $component->setRequireFieldMatch(false);
        $component->setQueryFieldPattern(['fieldC', 'fieldD']);
        $component->setUsePhraseHighlighter(true);
        $component->setHighlightMultiTerm(true);
        $component->setSnippets(2);
        $component->setFragSize(3);
        $component->setTagPrefix('<i>');
        $component->setTagPostfix('</i>');
        $component->setEncoder($component::ENCODER_HTML);
        $component->setMaxAnalyzedChars(100);
        $component->setMergeContiguous(true);
        $component->setAlternateField('title');
        $component->setMaxAlternateFieldLength(5);
        $component->setHighlightAlternate(false);
        $component->setFormatter($component::FORMATTER_SIMPLE);
        $component->setSimplePrefix('<b>');
        $component->setSimplePostfix('</b>');
        $component->setFragmenter($component::FRAGMENTER_REGEX);
        $component->setRegexSlop(1.3);
        $component->setRegexPattern('mypattern');
        $component->setRegexMaxAnalyzedChars(500);
        $component->setPreserveMulti(true);
        $component->setPayloads(false);

        $request = $builder->buildComponent($component, $request);

        $this->assertSame(
            [
                'hl' => 'true',
                'hl.method' => 'original',
                'hl.q' => 'text:myvalue',
                'hl.qparser' => 'myparser',
                'hl.requireFieldMatch' => 'false',
                'hl.queryFieldPattern' => 'fieldC,fieldD',
                'hl.usePhraseHighlighter' => 'true',
                'hl.highlightMultiTerm' => 'true',
                'hl.snippets' => 2,
                'hl.fragsize' => 3,
                'hl.tag.pre' => '<i>',
                'hl.tag.post' => '</i>',
                'hl.encoder' => 'html',
                'hl.maxAnalyzedChars' => 100,
                'hl.mergeContiguous' => 'true',
                'hl.alternateField' => 'title',
                'hl.maxAlternateFieldLength' => 5,
                'hl.highlightAlternate' => 'false',
                'hl.formatter' => 'simple',
                'hl.simple.pre' => '<b>',
                'hl.simple.post' => '</b>',
                'hl.fragmenter' => 'regex',
                'hl.regex.slop' => 1.3,
                'hl.regex.pattern' => 'mypattern',
                'hl.regex.maxAnalyzedChars' => 500,
                'hl.preserveMulti' => 'true',
                'hl.payloads' => 'false',
            ],
            $request->getParams()
        );
    }

    public function testBuildComponentWithFastVectorHighlighter()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setMethod($component::METHOD_FASTVECTOR);
        $component->setQuery('text:myvalue');
        $component->setQueryParser('myparser');
        $component->setRequireFieldMatch(false);
        $component->setQueryFieldPattern(['fieldC', 'fieldD']);
        $component->setUsePhraseHighlighter(true);
        $component->setHighlightMultiTerm(true);
        $component->setSnippets(2);
        $component->setFragSize(3);
        $component->setTagPrefix('<i>');
        $component->setTagPostfix('</i>');
        $component->setEncoder($component::ENCODER_HTML);
        $component->setMaxAnalyzedChars(100);
        $component->setAlternateField('title');
        $component->setMaxAlternateFieldLength(5);
        $component->setHighlightAlternate(false);
        $component->setFragListBuilder($component::FRAGLISTBUILDER_WEIGHTED);
        $component->setFragmentsBuilder($component::FRAGMENTSBUILDER_COLORED);
        $component->setBoundaryScanner($component::BOUNDARYSCANNER_SIMPLE);
        $component->setPhraseLimit(40);
        $component->setMultiValuedSeparatorChar('|');
        $component->setBoundaryScannerType($component::BOUNDARYSCANNER_TYPE_WORD);
        $component->setBoundaryScannerLanguage('en');
        $component->setBoundaryScannerCountry('BE');
        $component->setBoundaryScannerMaxScan(16);
        $component->setBoundaryScannerChars('.,');

        $request = $builder->buildComponent($component, $request);

        $this->assertSame(
            [
                'hl' => 'true',
                'hl.method' => 'fastVector',
                'hl.q' => 'text:myvalue',
                'hl.qparser' => 'myparser',
                'hl.requireFieldMatch' => 'false',
                'hl.queryFieldPattern' => 'fieldC,fieldD',
                'hl.usePhraseHighlighter' => 'true',
                'hl.highlightMultiTerm' => 'true',
                'hl.snippets' => 2,
                'hl.fragsize' => 3,
                'hl.tag.pre' => '<i>',
                'hl.tag.post' => '</i>',
                'hl.encoder' => 'html',
                'hl.maxAnalyzedChars' => 100,
                'hl.bs.language' => 'en', // these hl.bs.* parameters are not in the logical
                'hl.bs.country' => 'BE', // order for FastVector because they overlap with
                'hl.bs.type' => 'WORD', // the Unified Highlighter and are built in its order
                'hl.alternateField' => 'title',
                'hl.maxAlternateFieldLength' => 5,
                'hl.highlightAlternate' => 'false',
                'hl.fragListBuilder' => 'weighted',
                'hl.fragmentsBuilder' => 'colored',
                'hl.boundaryScanner' => 'simple',
                'hl.bs.maxScan' => 16,
                'hl.bs.chars' => '.,',
                'hl.phraseLimit' => 40,
                'hl.multiValuedSeparatorChar' => '|',
            ],
            $request->getParams()
        );
    }
}
