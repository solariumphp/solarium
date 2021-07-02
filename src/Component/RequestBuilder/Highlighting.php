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

        // set global highlighting params
        if (\count($component->getFields()) > 0) {
            $request->addParam('hl.fl', implode(',', array_keys($component->getFields())));
        }
        $request->addParam('hl.snippets', $component->getSnippets());
        $request->addParam('hl.fragsize', $component->getFragSize());
        $request->addParam('hl.mergeContiguous', $component->getMergeContiguous());
        $request->addParam('hl.requireFieldMatch', $component->getRequireFieldMatch());
        $request->addParam('hl.maxAnalyzedChars', $component->getMaxAnalyzedChars());
        $request->addParam('hl.alternateField', $component->getAlternateField());
        $request->addParam('hl.maxAlternateFieldLength', $component->getMaxAlternateFieldLength());
        $request->addParam('hl.preserveMulti', $component->getPreserveMulti());
        $request->addParam('hl.formatter', $component->getFormatter());
        $request->addParam('hl.simple.pre', $component->getSimplePrefix());
        $request->addParam('hl.simple.post', $component->getSimplePostfix());
        $request->addParam('hl.tag.pre', $component->getTagPrefix());
        $request->addParam('hl.tag.post', $component->getTagPostfix());
        $request->addParam('hl.fragmenter', $component->getFragmenter());
        $request->addParam('hl.fragListBuilder', $component->getFragListBuilder());
        $request->addParam('hl.fragmentsBuilder', $component->getFragmentsBuilder());
        $request->addParam('hl.useFastVectorHighlighter', $component->getUseFastVectorHighlighter());
        $request->addParam('hl.usePhraseHighlighter', $component->getUsePhraseHighlighter());
        $request->addParam('hl.highlightMultiTerm', $component->getHighlightMultiTerm());
        $request->addParam('hl.regex.slop', $component->getRegexSlop());
        $request->addParam('hl.regex.pattern', $component->getRegexPattern());
        $request->addParam('hl.regex.maxAnalyzedChars', $component->getRegexMaxAnalyzedChars());
        $request->addParam('hl.q', $component->getQuery());
        $request->addParam('hl.phraseLimit', $component->getPhraseLimit());
        $request->addParam('hl.multiValuedSeparatorChar', $component->getMultiValuedSeparatorChar());
        $request->addParam('hl.bs.maxScan', $component->getBoundaryScannerMaxScan());
        $request->addParam('hl.bs.chars', $component->getBoundaryScannerChars());
        $request->addParam('hl.bs.type', $component->getBoundaryScannerType());
        $request->addParam('hl.bs.language', $component->getBoundaryScannerLanguage());
        $request->addParam('hl.bs.country', $component->getBoundaryScannerCountry());
        $request->addParam('h1.method', $component->getMethod());

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
    protected function addFieldParams($field, $request)
    {
        $prefix = 'f.'.$field->getName().'.hl.';
        $request->addParam($prefix.'snippets', $field->getSnippets());
        $request->addParam($prefix.'fragsize', $field->getFragSize());
        $request->addParam($prefix.'mergeContiguous', $field->getMergeContiguous());
        $request->addParam($prefix.'alternateField', $field->getAlternateField());
        $request->addParam($prefix.'preserveMulti', $field->getPreserveMulti());
        $request->addParam($prefix.'formatter', $field->getFormatter());
        $request->addParam($prefix.'simple.pre', $field->getSimplePrefix());
        $request->addParam($prefix.'simple.post', $field->getSimplePostfix());
        $request->addParam($prefix.'fragmenter', $field->getFragmenter());
        $request->addParam($prefix.'useFastVectorHighlighter', $field->getUseFastVectorHighlighter());
    }
}
