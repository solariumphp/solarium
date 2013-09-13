<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 * Copyright 2012 Alexander Brausewetter. All rights reserved.
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
 * @copyright Copyright 2012 Alexander Brausewetter <alex@helpdeskhq.com>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Client
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Extract;

use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\RequestBuilder as BaseRequestBuilder;
use Solarium\Core\Client\Request;
use Solarium\Exception\RuntimeException;

/**
 * Build an extract request
 *
 * @package Solarium
 * @subpackage Client
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build the request
     *
     * @throws RuntimeException
     * @param  Query|QueryInterface $query
     * @return Request
     */
    public function build(QueryInterface $query)
    {
        $request = parent::build($query);
        $request->setMethod(Request::METHOD_POST);

        // add common options to request
        $request->addParam('commit', $query->getCommit());
        $request->addParam('commitWithin', $query->getCommitWithin());

        $request->addParam('uprefix', $query->getUprefix());
        $request->addParam('lowernames', $query->getLowernames());
        $request->addParam('defaultField', $query->getDefaultField());

        foreach ($query->getFieldMappings() as $fromField => $toField) {
            $request->addParam('fmap.' . $fromField, $toField);
        }

        // add document settings to request
        if (($doc = $query->getDocument()) != null) {
            if ($doc->getBoost() !== null) {
                throw new RuntimeException('Extract does not support document-level boosts, use field boosts instead.');
            }

            // literal.*
            foreach ($doc->getFields() as $name => $value) {
                $value = (array) $value;
                foreach ($value as $multival) {
                    $request->addParam('literal.' . $name, $multival);
                }
            }

            // boost.*
            foreach ($doc->getFieldBoosts() as $name => $value) {
                $request->addParam('boost.' . $name, $value);
            }
        }

        // add file to request
        $request->setFileUpload($query->getFile());
        $request->addParam('resource.name', basename($query->getFile()));
        $request->addHeader('Content-Type: multipart/form-data');

        return $request;
    }
}
