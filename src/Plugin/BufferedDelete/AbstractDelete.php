<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedDelete;

/**
 * Delete base class.
 */
abstract class AbstractDelete
{
    /**
     * Delete by id.
     */
    public const TYPE_ID = 'id';

    /**
     * Delete by query.
     */
    public const TYPE_QUERY = 'query';

    /**
     * Get delete type.
     *
     * @return string
     */
    abstract public function getType(): string;

    abstract public function __toString(): string;
}
