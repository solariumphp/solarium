<?php

namespace Solarium\Plugin\MinimumScoreFilter;

use Solarium\Core\Plugin\AbstractPlugin;

/**
 * MinimumScoreFilter plugin.
 *
 * Filters results based on score relative to the maxScore
 */
class MinimumScoreFilter extends AbstractPlugin
{
    /**
     * Custom query type name.
     */
    const QUERY_TYPE = 'minimum-score-select';

    /**
     * Plugin init function.
     *
     * Register event listeners
     */
    protected function initPluginType()
    {
        $this->client->registerQueryType(self::QUERY_TYPE, 'Solarium\Plugin\MinimumScoreFilter\Query');
    }
}
