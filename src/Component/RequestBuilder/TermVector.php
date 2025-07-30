<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\TermVector as TermVectorComponent;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Build a Term Vector query request.
 */
class TermVector implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for Term Vector.
     *
     * @param TermVectorComponent $component
     * @param Request             $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        $request->addParam('tv', true);

        foreach ($component->getDocIds() as $docId) {
            $request->addParam('tv.docIds', trim($docId));
        }

        foreach ($component->getFields() as $field) {
            $request->addParam('tv.fl', trim($field));
        }

        $request->addParam('tv.all', $component->getAll());
        $request->addParam('tv.df', $component->getDocumentFrequency());
        $request->addParam('tv.offsets', $component->getOffsets());
        $request->addParam('tv.positions', $component->getPositions());
        $request->addParam('tv.payloads', $component->getPayloads());
        $request->addParam('tv.tf', $component->getTermFrequency());
        $request->addParam('tv.tf_idf', $component->getTermFreqInverseDocFreq());

        return $request;
    }
}
