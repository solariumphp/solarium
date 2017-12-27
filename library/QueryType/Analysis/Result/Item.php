<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
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
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\QueryType\Analysis\Result;

/**
 * Analysis item.
 */
class Item
{
    /**
     * Text string.
     *
     * @var string
     */
    protected $text;

    /**
     * RawText string.
     *
     * @var string
     */
    protected $rawText;

    /**
     * Start.
     *
     * @var int
     */
    protected $start;

    /**
     * End.
     *
     * @var int
     */
    protected $end;

    /**
     * Position.
     *
     * @var int
     */
    protected $position;

    /**
     * Position history.
     *
     * @var array
     */
    protected $positionHistory;

    /**
     * Type.
     *
     * @var string
     */
    protected $type;

    /**
     * Match.
     *
     * @var boolean
     */
    protected $match = false;

    /**
     * Constructor.
     *
     * @param array $analysis
     */
    public function __construct($analysis)
    {
        $this->text = $analysis['text'];
        $this->start = $analysis['start'];
        $this->end = $analysis['end'];
        $this->position = $analysis['position'];
        $this->positionHistory = $analysis['positionHistory'];
        $this->type = $analysis['type'];

        if (isset($analysis['raw_text'])) {
            $this->rawText = $analysis['raw_text'];
        }

        if (isset($analysis['match'])) {
            $this->match = $analysis['match'];
        }
    }

    /**
     * Get text value.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get raw text value.
     *
     * This values is not available in all cases
     *
     * @return string
     */
    public function getRawText()
    {
        return $this->rawText;
    }

    /**
     * Get start value.
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get end value.
     *
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Get postion value.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Get position history value.
     *
     * @return array
     */
    public function getPositionHistory()
    {
        if (is_array($this->positionHistory)) {
            return $this->positionHistory;
        } else {
            return array();
        }
    }

    /**
     * Get type value.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get match value.
     *
     * @return boolean
     */
    public function getMatch()
    {
        return $this->match;
    }
}
