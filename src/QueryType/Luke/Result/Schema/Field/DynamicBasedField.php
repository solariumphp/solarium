<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Schema\Field;

/**
 * Field created from a dynamic field definition.
 *
 * Used for fields that are a copy source or destination, and aren't
 * explicitly defined in the schema.
 */
class DynamicBasedField extends AbstractField implements CopyFieldDestInterface, CopyFieldSourceInterface
{
    /**
     * @var DynamicField
     */
    protected $dynamicBase;

    /**
     * @return DynamicField
     */
    public function &getDynamicBase(): DynamicField
    {
        return $this->dynamicBase;
    }

    /**
     * @param DynamicField $dynamicBase
     *
     * @return self Provides fluent interface
     */
    public function setDynamicBase(DynamicField &$dynamicBase): self
    {
        $this->dynamicBase = &$dynamicBase;

        return $this;
    }
}
