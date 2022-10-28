<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\Highlighting\Field as HighlightingField;
use Solarium\Component\Highlighting\Highlighting as HighlightingComponent;
use Solarium\Component\Highlighting\HighlightingInterface;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Add select component Highlighting to the request.
 */
class Highlighting implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for Highlighting.
     *
     * @param HighlightingComponent $component
     * @param Request               $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        // enable highlighting
        $request->addParam('hl', 'true');
        $request->addParam('hl.useFastVectorHighlighter', $component->getUseFastVectorHighlighter());
        $request->addParam('hl.method', $component->getMethod());

        // set global highlighting params
        if (0 !== \count($component->getFields())) {
            $request->addParam('hl.fl', implode(',', array_keys($component->getFields())));
        }
        $request->addParam('hl.q', $component->getQuery());
        $request->addParam('hl.qparser', $component->getQueryParser());
        $request->addParam('hl.requireFieldMatch', $component->getRequireFieldMatch());
        $request->addParam('hl.queryFieldPattern', null === ($qfp = $component->getQueryFieldPattern()) ? null : implode(',', $qfp));
        $this->addHighlighterParams($component, $request);

        // set per-field highlighting params
        foreach ($component->getFields() as $field) {
            $this->addFieldParams($field, $request);
        }

        return $request;
    }

    /**
     * Add per-field override options to the request.
     *
     * @param HighlightingField $field
     * @param Request           $request
     */
    protected function addFieldParams(HighlightingField $field, Request $request): void
    {
        $prefix = 'f.'.$field->getName().'.';
        $request->addParam($prefix.'hl.useFastVectorHighlighter', $field->getUseFastVectorHighlighter());
        $request->addParam($prefix.'hl.method', $field->getMethod());
        $this->addHighlighterParams($field, $request, $prefix);
    }

    /**
     * Add common and specific highlighter options to the request.
     *
     * @param HighlightingInterface $hlt
     * @param Request               $request
     * @param string                $prefix  Prefix to use for parameter names
     */
    protected function addHighlighterParams(HighlightingInterface $hlt, Request $request, string $prefix = ''): void
    {
        // common highlighter params
        $request->addParam($prefix.'hl.usePhraseHighlighter', $hlt->getUsePhraseHighlighter());
        $request->addParam($prefix.'hl.highlightMultiTerm', $hlt->getHighlightMultiTerm());
        $request->addParam($prefix.'hl.snippets', $hlt->getSnippets());
        $request->addParam($prefix.'hl.fragsize', $hlt->getFragSize());
        $request->addParam($prefix.'hl.tag.pre', $hlt->getTagPrefix());
        $request->addParam($prefix.'hl.tag.post', $hlt->getTagPostfix());
        $request->addParam($prefix.'hl.encoder', $hlt->getEncoder());
        $request->addParam($prefix.'hl.maxAnalyzedChars', $hlt->getMaxAnalyzedChars());

        // unified highlighter params
        $request->addParam($prefix.'hl.offsetSource', $hlt->getOffsetSource());
        $request->addParam($prefix.'hl.fragAlignRatio', $hlt->getFragAlignRatio());
        $request->addParam($prefix.'hl.fragsizeIsMinimum', $hlt->getFragsizeIsMinimum());
        $request->addParam($prefix.'hl.tag.ellipsis', $hlt->getTagEllipsis());
        $request->addParam($prefix.'hl.defaultSummary', $hlt->getDefaultSummary());
        $request->addParam($prefix.'hl.score.k1', $hlt->getScoreK1());
        $request->addParam($prefix.'hl.score.b', $hlt->getScoreB());
        $request->addParam($prefix.'hl.score.pivot', $hlt->getScorePivot());
        $request->addParam($prefix.'hl.bs.language', $hlt->getBoundaryScannerLanguage());
        $request->addParam($prefix.'hl.bs.country', $hlt->getBoundaryScannerCountry());
        $request->addParam($prefix.'hl.bs.variant', $hlt->getBoundaryScannerVariant());
        $request->addParam($prefix.'hl.bs.type', $hlt->getBoundaryScannerType());
        $request->addParam($prefix.'hl.bs.separator', $hlt->getBoundaryScannerSeparator());
        $request->addParam($prefix.'hl.weightMatches', $hlt->getWeightMatches());

        // original highlighter params
        $request->addParam($prefix.'hl.mergeContiguous', $hlt->getMergeContiguous());
        $request->addParam($prefix.'hl.maxMultiValuedToExamine', $hlt->getMaxMultiValuedToExamine());
        $request->addParam($prefix.'hl.maxMultiValuedToMatch', $hlt->getMaxMultiValuedToMatch());
        $request->addParam($prefix.'hl.alternateField', $hlt->getAlternateField());
        $request->addParam($prefix.'hl.maxAlternateFieldLength', $hlt->getMaxAlternateFieldLength());
        $request->addParam($prefix.'hl.highlightAlternate', $hlt->getHighlightAlternate());
        $request->addParam($prefix.'hl.formatter', $hlt->getFormatter());
        $request->addParam($prefix.'hl.simple.pre', $hlt->getSimplePrefix());
        $request->addParam($prefix.'hl.simple.post', $hlt->getSimplePostfix());
        $request->addParam($prefix.'hl.fragmenter', $hlt->getFragmenter());
        $request->addParam($prefix.'hl.regex.slop', $hlt->getRegexSlop());
        $request->addParam($prefix.'hl.regex.pattern', $hlt->getRegexPattern());
        $request->addParam($prefix.'hl.regex.maxAnalyzedChars', $hlt->getRegexMaxAnalyzedChars());
        $request->addParam($prefix.'hl.preserveMulti', $hlt->getPreserveMulti());
        $request->addParam($prefix.'hl.payloads', $hlt->getPayloads());

        // fastvector highlighter params
        $request->addParam($prefix.'hl.fragListBuilder', $hlt->getFragListBuilder());
        $request->addParam($prefix.'hl.fragmentsBuilder', $hlt->getFragmentsBuilder());
        $request->addParam($prefix.'hl.boundaryScanner', $hlt->getBoundaryScanner());
        $request->addParam($prefix.'hl.bs.maxScan', $hlt->getBoundaryScannerMaxScan());
        $request->addParam($prefix.'hl.bs.chars', $hlt->getBoundaryScannerChars());
        $request->addParam($prefix.'hl.phraseLimit', $hlt->getPhraseLimit());
        $request->addParam($prefix.'hl.multiValuedSeparatorChar', $hlt->getMultiValuedSeparatorChar());
    }
}
