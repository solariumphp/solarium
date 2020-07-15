<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query;

/**
 * Solr document interface.
 */
interface DocumentInterface
{
    /**
     * Get all fields.
     *
     * @return array
     */
    public function getFields(): array;
}
