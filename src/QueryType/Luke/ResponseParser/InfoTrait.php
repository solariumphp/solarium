<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\ResponseParser;

use Solarium\QueryType\Luke\Result\Info\Info as InfoResult;

/**
 * Parse Luke info response data and use the result to parse flags from other response data.
 */
trait InfoTrait
{
    /**
     * A key to what each character in the field flags means.
     *
     * @var array
     */
    protected $key;

    /**
     * @param array $infoData
     *
     * @return InfoResult
     */
    protected function parseInfo(array $infoData): InfoResult
    {
        $info = new InfoResult();

        $info->setKey($infoData['key']);
        $info->setNote($infoData['NOTE']);

        // for lookups when parsing flags
        $this->key = $info->getKey();

        return $info;
    }
}
