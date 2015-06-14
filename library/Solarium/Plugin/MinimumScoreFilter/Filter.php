<?php
/**
 * Copyright 2014 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2014 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\Plugin\MinimumScoreFilter;

use Solarium\Exception\OutOfBoundsException;

/**
 * Minimumscore filter.
 */
class Filter
{
    /**
     * Apply filter to document array.
     *
     * @param array  $documents
     * @param float  $maxScore
     * @param float  $ratio
     * @param string $mode
     *
     * @return array
     */
    public function filterDocuments($documents, $maxScore, $ratio, $mode)
    {
        $threshold = $maxScore * $ratio;

        switch ($mode) {
            case Query::FILTER_MODE_REMOVE:
                foreach ($documents as $key => $document) {
                    if ($document->score < $threshold) {
                        unset($documents[$key]);
                    }
                }
                break;
            case Query::FILTER_MODE_MARK:
                foreach ($documents as $key => $document) {
                    $documents[$key] = new Document($document, $threshold);
                }
                break;
            default:
                throw new OutOfBoundsException('Unknown filter mode in query: '.$mode);
                break;
        }

        return $documents;
    }
}
