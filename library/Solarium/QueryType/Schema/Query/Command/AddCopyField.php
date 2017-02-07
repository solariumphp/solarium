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
namespace Solarium\QueryType\Schema\Query\Command;

use Solarium\Core\ArrayableInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Schema\Query\Field\CopyField;
use Solarium\QueryType\Schema\Query\Query as SchemaQuery;

/**
 * Class AddCopyField
 * @author Beno!t POLASZEK
 */
class AddCopyField extends Command implements ArrayableInterface {

    /**
     * @var CopyField[]
     */
    protected $fields = array();

    /**
     * Returns command type, for use in adapters
     *
     * @return string
     */
    public function getType() {
        return SchemaQuery::COMMAND_ADD_COPY_FIELD;
    }

    /**
     * @return CopyField[]
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * @param CopyField[] $fields
     * @return $this - Provides Fluent Interface
     */
    public function setFields(array $fields) {
        $this->fields = array();
        $this->addFields($fields);
        return $this;
    }

    /**
     * @param CopyField[] $fields
     * @return $this - Provides Fluent Interface
     */
    public function addFields(array $fields) {
        foreach ($fields AS $field)
            is_array($field) ? $this->createField($field) : $this->addField($field);
        return $this;
    }

    /**
     * @param CopyField $field
     * @return $this
     */
    public function addField(CopyField $field) {
        if (!array_key_exists((string) $field, $this->getFields()))
            $this->fields[(string) $field] = $field;
        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function createField(array $attributes = array()) {
        if (!array_key_exists('source', $attributes))
            throw new RuntimeException("A copyField must have a source attribute.");
        if (!array_key_exists('dest', $attributes))
            throw new RuntimeException("A copyField must have a dest attribute.");
        $copyField = new CopyField($attributes['source'], $attributes['dest'], isset($attributes['maxChars']) ? $attributes['maxChars'] : null);
        $this->addField($copyField);
        return $copyField;
    }

    /**
     * @return array
     */
    public function castAsArray() {
        return array_values(array_map(function (CopyField $copyField) {
            return $copyField->castAsArray();
        }, $this->getFields()));
    }

}