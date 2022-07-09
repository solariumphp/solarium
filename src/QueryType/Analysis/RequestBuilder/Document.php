<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Analysis\RequestBuilder;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\Analysis\Query\Document as QueryDocument;

/**
 * Build a document analysis request.
 */
class Document extends BaseRequestBuilder
{
    /**
     * Build request for an analysis document query.
     *
     * @param AbstractQuery|QueryInterface|QueryDocument $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);
        $request->setRawData($this->getRawData($query));
        $request->setMethod(Request::METHOD_POST);
        $request->setContentType(Request::CONTENT_TYPE_APPLICATION_XML);

        return $request;
    }

    /**
     * Create the raw post data (xml).
     *
     * @param QueryDocument $query
     *
     * @return string
     */
    public function getRawData(QueryDocument $query): string
    {
        $xml = '<docs>';

        foreach ($query->getDocuments() as $doc) {
            $xml .= '<doc>';

            foreach ($doc->getFields() as $name => $value) {
                if (\is_array($value)) {
                    foreach ($value as $multival) {
                        $xml .= $this->buildFieldXml($name, $multival);
                    }
                } else {
                    $xml .= $this->buildFieldXml($name, $value);
                }
            }

            $xml .= '</doc>';
        }

        $xml .= '</docs>';

        return $xml;
    }

    /**
     * Build XML for a field.
     *
     * @param string $name
     * @param string $value
     *
     * @return string
     */
    protected function buildFieldXml(string $name, string $value): string
    {
        $helper = $this->getHelper();

        return '<field name="'.$name.'">'.$helper->escapeXMLCharacterData($helper->filterControlCharacters($value)).'</field>';
    }
}
