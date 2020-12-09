<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Extract;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\Exception\RuntimeException;

/**
 * Build an extract request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build the request.
     *
     * @param Query|QueryInterface $query
     *
     * @throws RuntimeException
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);
        $request->setMethod(Request::METHOD_POST);

        // add common options to request
        $request->addParam('commit', $query->getCommit());
        $request->addParam('commitWithin', $query->getCommitWithin());

        $request->addParam('uprefix', $query->getUprefix());
        $request->addParam('lowernames', $query->getLowernames());
        $request->addParam('defaultField', $query->getDefaultField());
        $request->addParam('extractOnly', $query->getExtractOnly());
        $request->addParam('extractFormat', $query->getExtractFormat());

        foreach ($query->getFieldMappings() as $fromField => $toField) {
            $request->addParam('fmap.'.$fromField, $toField);
        }

        // add document settings to request
        /** @var \Solarium\QueryType\Update\Query\Document $doc */
        if (null !== ($doc = $query->getDocument())) {
            if (null !== $doc->getBoost()) {
                throw new RuntimeException('Extract does not support document-level boosts, use field boosts instead.');
            }

            // literal.*
            foreach ($doc->getFields() as $name => $value) {
                if ($value instanceof \DateTimeInterface) {
                    $value = $query->getHelper()->formatDate($value);
                }
                $value = (array) $value;
                foreach ($value as $multival) {
                    $request->addParam('literal.'.$name, $multival);
                }
            }

            // boost.*
            foreach ($doc->getFieldBoosts() as $name => $value) {
                $request->addParam('boost.'.$name, $value);
            }
        }

        // add file to request
        $file = $query->getFile();
        if (preg_match('/^(http|https):\/\/(.+)/i', $file)) {
            $request->addParam('stream.url', $file);
            $request->setMethod(Request::METHOD_GET);
        } elseif (is_readable($file)) {
            $request->setFileUpload($file);
            $request->addParam('resource.name', basename($query->getFile()));
            $request->addHeader('Content-Type: multipart/form-data; boundary='.$request->getHash());
        } else {
            throw new RuntimeException(sprintf('Extract query file path/url invalid or not available: %s', $file));
        }

        return $request;
    }
}
