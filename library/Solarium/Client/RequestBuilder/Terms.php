<?php
/**
 * Copyright 2011 Bas de Nooijer.
 * Copyright 2011 Gasol Wu. PIXNET Digital Media Corporation.
 * All rights reserved.
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
 * @copyright Copyright 2011 Gasol Wu <gasol.wu@gmail.com>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Client
 */

/**
 * Build a Terms query request
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_RequestBuilder_Terms extends Solarium_Client_RequestBuilder
{

    /**
     * Build request for a Terms query
     *
     * @param Solarium_Query_Terms $query
     * @return Solarium_Client_Request
     */
    public function build($query)
    {
        $request = parent::build($query);
        $request->addParam('terms', true);
        $request->addParam('terms.lower', $query->getLowerbound());
        $request->addParam('terms.lower.incl', $query->getLowerboundInclude());
        $request->addParam('terms.mincount', $query->getMinCount());
        $request->addParam('terms.maxcount', $query->getMaxCount());
        $request->addParam('terms.prefix', $query->getPrefix());
        $request->addParam('terms.regex', $query->getRegex());
        $request->addParam('terms.limit', $query->getLimit());
        $request->addParam('terms.upper', $query->getUpperbound());
        $request->addParam('terms.upper.incl', $query->getUpperboundInclude());
        $request->addParam('terms.raw', $query->getRaw());
        $request->addParam('terms.sort', $query->getSort());

        $fields = explode(',', $query->getFields());
        foreach ($fields as $field) {
            $request->addParam('terms.fl', trim($field));
        }

        if ($query->getRegexFlags() !== null) {
            $flags = explode(',', $query->getRegexFlags());
            foreach ($flags as $flag) {
                $request->addParam('terms.regex.flag', trim($flag));
            }
        }

        return $request;
    }

}
