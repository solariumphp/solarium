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
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Schema;

use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\ResponseParser as ResponseParserAbstract;
use Solarium\QueryType\Schema\Query\Field\Field;

/**
 * Parse schema response data
 * @author Beno!t POLASZEK
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Parse response data
     *
     * @param  Result $result
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();

        $errors             =   isset($data['errors']) ? $data['errors'] : null;

        $name               =   isset($data['schema']['name']) ? $data['schema']['name'] : null;
        $version            =   isset($data['schema']['version']) ? $data['schema']['version'] : null;
        $uniqueKey          =   isset($data['schema']['uniqueKey']) ? $data['schema']['uniqueKey'] : null;
        $defaultSearchField =   isset($data['schema']['defaultSearchField']) ? $data['schema']['defaultSearchField'] : null;

        $fields = array();
        if (isset($data['schema']['fields']))
            foreach ($data['schema']['fields'] AS $field)
                $fields[$field['name']] = new Field($field);

        return $this->addHeaderInfo($data, array(
            'errors' => $errors,
            'name' => $name,
            'version' => $version,
            'uniqueKey' => $uniqueKey,
            'defaultSearchField' => $defaultSearchField,
            'fields' => $fields,
        ));
    }
}
