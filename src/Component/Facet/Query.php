<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;
use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Core\Query\Helper;

/**
 * Facet query.
 *
 * @see https://solr.apache.org/guide/faceting.html#general-facet-parameters
 */
class Query extends AbstractFacet implements QueryInterface
{
    use QueryTrait;

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'query' => '*:*',
    ];

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
    {
        return FacetSetInterface::FACET_QUERY;
    }

    /**
     * Returns a query helper.
     *
     * @return \Solarium\Core\Query\Helper
     */
    public function getHelper(): Helper
    {
        return new Helper();
    }
}
