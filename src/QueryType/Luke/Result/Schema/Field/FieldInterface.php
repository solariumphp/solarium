<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Schema\Field;

/**
 * Field interface.
 */
interface FieldInterface
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name);

    /**
     * @return string
     */
    public function getName(): string;

    public function __toString(): string;
}
