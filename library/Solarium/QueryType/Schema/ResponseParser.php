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
 * @license   http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link      http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Schema;

use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\ResponseParser as ResponseParserAbstract;
use Solarium\QueryType\Schema\Query\Field\CopyField;
use Solarium\QueryType\Schema\Query\Field\Field;
use Solarium\QueryType\Schema\Query\FieldType\Analyzer\AnalyzerInterface;
use Solarium\QueryType\Schema\Query\FieldType\Analyzer\Tokenizer\Tokenizer;
use Solarium\QueryType\Schema\Query\FieldType\FieldType;

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

        $errors = isset($data['errors']) ? $data['errors'] : null;

        $name = isset($data['schema']['name']) ? $data['schema']['name'] : null;
        $version = isset($data['schema']['version']) ? $data['schema']['version'] : null;
        $uniqueKey = isset($data['schema']['uniqueKey']) ? $data['schema']['uniqueKey'] : null;
        $defaultSearchField = isset($data['schema']['defaultSearchField']) ? $data['schema']['defaultSearchField'] : null;

        $fieldTypes = array();

        if (isset($data['schema']['fieldTypes'])) {

            foreach ($data['schema']['fieldTypes'] AS $fieldType) {

                $_fieldType = new FieldType($fieldType['name'], $fieldType['class']);

                foreach ($fieldType AS $key => $value) {

                    if (in_array($key, array('analyzer', 'indexAnalyzer', 'queryAnalyzer')) && $value instanceof AnalyzerInterface) {
                        $_fieldType->addAnalyzer($value);
                    } elseif (in_array($key, array('analyzer', 'indexAnalyzer', 'queryAnalyzer')) && is_array($value)) {
                        switch ($key) {
                            case 'indexAnalyzer':
                                $analyzer = 'index';
                                break;
                            case 'queryAnalyzer':
                                $analyzer = 'query';
                                break;
                            default:
                            case 'analyzer':
                                $analyzer = null;
                                break;
                        }

                        $analyzer = $_fieldType->createAnalyzer($analyzer);

                        if (!empty($value['class'])) {
                            $analyzer->setClass($value['class']);
                        }

                        if (!empty($value['tokenizer'])) {
                            $analyzer->setTokenizer(new Tokenizer(!empty($value['tokenizer']['class']) ? $value['tokenizer']['class'] : null, !empty($value['tokenizer']['delimiter']) ? $value['tokenizer']['delimiter'] : null));
                        }

                        if (!empty($value['filters'])) {
                            foreach ($value['filters'] AS $filter) {
                                $_filter = $analyzer->createFilter($filter['class']);
                                foreach ($filter AS $attrName => $attrValue) {
                                    if ($attrName != 'class') {
                                        $_filter->addAttribute($attrName, $attrValue);
                                    }
                                }
                            }

                        }

                    } else {
                        $_fieldType[$key] = $value;
                    }
                }

                $fieldTypes[$fieldType['name']] = $_fieldType;
            }
        }

        $fields = array();
        if (isset($data['schema']['fields'])) {
            foreach ($data['schema']['fields'] AS $field) {
                $fields[$field['name']] = new Field($field);
            }
        }

        $dynamicFields = array();
        if (isset($data['schema']['dynamicFields'])) {
            foreach ($data['schema']['dynamicFields'] AS $field) {
                $dynamicFields[$field['name']] = new Field($field);
            }
        }

        $copyFields = array();
        if (isset($data['schema']['copyFields'])) {

            foreach ($data['schema']['copyFields'] AS $field) {
                if (array_key_exists($field['source'], $copyFields)) {
                    $copyFields[$field['source']]->addDest($field['dest']);
                } else {
                    $copyFields[$field['source']] = new CopyField($field['source'], $field['dest']);
                    if (isset($field['maxChars'])) {
                        $copyFields[$field['source']]->setMaxChars($field['maxChars']);
                    }
                }
            }
        }

        return $this->addHeaderInfo($data, array(
            'errors'             => $errors,
            'name'               => $name,
            'version'            => $version,
            'uniqueKey'          => $uniqueKey,
            'defaultSearchField' => $defaultSearchField,
            'fieldTypes'         => $fieldTypes,
            'fields'             => $fields,
            'dynamicFields'      => $dynamicFields,
            'copyFields'         => $copyFields,
        ));
    }
}
