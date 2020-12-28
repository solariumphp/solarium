<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query\QueryParser;

/**
 * Geofilt.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 *
 * @see https://lucene.apache.org/solr/guide/8_6/spatial-search.html#geofilt
 */
final class GeofiltQueryParser extends AbstractSpacialParser
{
    protected const TYPE = 'geofilt';
}
