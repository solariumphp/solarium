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
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Update\Query\Command;

use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query commit command
 *
 * @link http://wiki.apache.org/solr/UpdateXmlMessages#A.22commit.22_and_.22optimize.22
 */
class Commit extends Command
{
    /**
     * Get command type
     *
     * @return string
     */
    public function getType()
    {
        return UpdateQuery::COMMAND_COMMIT;
    }

    /**
     * Get softCommit option
     *
     * @return boolean
     */
    public function getSoftCommit()
    {
        return $this->getOption('softcommit');
    }

    /**
     * Set softCommit option
     *
     * @param  boolean $softCommit
     * @return self    Provides fluent interface
     */
    public function setSoftCommit($softCommit)
    {
        return $this->setOption('softcommit', $softCommit);
    }

    /**
     * Get waitSearcher option
     *
     * @return boolean
     */
    public function getWaitSearcher()
    {
        return $this->getOption('waitsearcher');
    }

    /**
     * Set waitSearcher option
     *
     * @param  boolean $waitSearcher
     * @return self    Provides fluent interface
     */
    public function setWaitSearcher($waitSearcher)
    {
        return $this->setOption('waitsearcher', $waitSearcher);
    }

    /**
     * Get expungeDeletes option
     *
     * @return boolean
     */
    public function getExpungeDeletes()
    {
        return $this->getOption('expungedeletes');
    }

    /**
     * Set expungeDeletes option
     *
     * @param  boolean $expungeDeletes
     * @return self    Provides fluent interface
     */
    public function setExpungeDeletes($expungeDeletes)
    {
        return $this->setOption('expungedeletes', $expungeDeletes);
    }
}
