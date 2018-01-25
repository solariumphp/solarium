<?php

namespace Solarium\Component\Result\Facet;

/**
 * Select multiquery facet result.
 *
 * A multiquery facet will usually return a dataset of multiple rows, in each
 * row a query key and its count. You can access the values as an array using
 * {@link getValues()} or iterate this object.
 */
class MultiQuery extends Field
{
}
