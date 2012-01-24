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
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Result
 */

/**
 * Analysis item
 *
 * @package Solarium
 * @subpackage Result
 */
class Solarium_Result_Analysis_Item
{

    /**
     * Text string
     *
     * @var string
     */
    protected $_text;

    /**
     * RawText string
     *
     * @var string
     */
    protected $_rawText;

    /**
     * Start
     *
     * @var int
     */
    protected $_start;

    /**
     * End
     *
     * @var int
     */
    protected $_end;

    /**
     * Position
     *
     * @var int
     */
    protected $_position;

    /**
     * Position history
     *
     * @var array
     */
    protected $_positionHistory;

    /**
     * Type
     *
     * @var string
     */
    protected $_type;

    /**
     * Match
     *
     * @var boolean
     */
    protected $_match = false;

    /**
     * Constructor
     *
     * @param array $analysis
     */
    public function __construct($analysis)
    {
        $this->_text = $analysis['text'];
        $this->_start = $analysis['start'];
        $this->_end = $analysis['end'];
        $this->_position = $analysis['position'];
        $this->_positionHistory = $analysis['positionHistory'];
        $this->_type = $analysis['type'];

        if (isset($analysis['raw_text'])) {
            $this->_rawText = $analysis['raw_text'];
        }

        if (isset($analysis['match'])) {
            $this->_match = $analysis['match'];
        }
    }

    /**
     * Get text value
     *
     * @return string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Get raw text value
     *
     * This values is not available in all cases
     *
     * @return string
     */
    public function getRawText()
    {
        return $this->_rawText;
    }

    /**
     * Get start value
     *
     * @return int
     */
    public function getStart()
    {
        return $this->_start;
    }

    /**
     * Get end value
     *
     * @return int
     */
    public function getEnd()
    {
        return $this->_end;
    }

    /**
     * Get postion value
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_position;
    }

    /**
     * Get position history value
     *
     * @return array
     */
    public function getPositionHistory()
    {
        return $this->_positionHistory;
    }

    /**
     * Get type value
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get match value
     *
     * @return boolean
     */
    public function getMatch()
    {
        return $this->_match;
    }

}