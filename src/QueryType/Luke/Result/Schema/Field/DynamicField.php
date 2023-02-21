<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Schema\Field;

/**
 * Retrieved dynamic field definition.
 *
 * @see https://solr.apache.org/guide/dynamic-fields.html
 */
class DynamicField extends AbstractField implements CopyFieldDestInterface, CopyFieldSourceInterface, SchemaFieldInterface
{
    /**
     * Create a {@see DynamicBasedField} from this dynamic field definition.
     *
     * @param string $name
     *
     * @return DynamicBasedField
     */
    public function createField(string $name): DynamicBasedField
    {
        $field = new DynamicBasedField($name);

        $field->setType($this->getType());
        $field->setFlags($this->getFlags());
        $field->setPositionIncrementGap($this->getPositionIncrementGap());
        $field->setDynamicBase($this);

        return $field;
    }
}
