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
namespace Solarium\QueryType\System;
use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * System query result.
 */
class Result extends BaseResult
{
    /**
     * Ensures the response is parsed and returns a property.
     *
     * @param  string $property The name of the class member variable.
     * @return mixed            The value of the property.
     */
    public function returnProperty($property)
    {
        $this->parseResponse();
        return $this->$property;
    }

    /**
     * @return string
     */
    public function getCoreSchema()
    {
        return $this->returnProperty('CoreSchema');
    }

    /**
     * @return string
     */
    public function getCoreHost()
    {
        return $this->returnProperty('CoreHost');
    }

    /**
     * @return string
     */
    public function getCoreNow()
    {
        return $this->returnProperty('CoreNow');
    }

    /**
     * @return string
     */
    public function getCoreStart()
    {
        return $this->returnProperty('CoreStart');
    }

    /**
     * @return string
     */
    public function getCoreDirectoryInstance()
    {
        return $this->returnProperty('CoreDirectoryInstance');
    }

    /**
     * @return string
     */
    public function getCoreDirectoryData()
    {
        return $this->returnProperty('CoreDirectoryData');
    }

    /**
     * @return string
     */
    public function getCoreDirectoryIndex()
    {
        return $this->returnProperty('CoreDirectoryIndex');
    }

    /**
     * @return string
     */
    public function getLuceneSolrSpecVersion()
    {
        return $this->returnProperty('LuceneSolrSpecVersion');
    }

    /**
     * @return string
     */
    public function getLuceneSolrImplVersion()
    {
        return $this->returnProperty('LuceneSolrImplVersion');
    }

    /**
     * @return string
     */
    public function getLuceneSpecVersion()
    {
        return $this->returnProperty('LuceneSpecVersion');
    }

    /**
     * @return string
     */
    public function getLuceneImplVersion()
    {
        return $this->returnProperty('LuceneImplVersion');
    }

    /**
     * @return string
     */
    public function getJvmVersion()
    {
        return $this->returnProperty('JvmVersion');
    }

    /**
     * @return string
     */
    public function getJvmName()
    {
        return $this->returnProperty('JvmName');
    }

    /**
     * @return int
     */
    public function getJvmProcessors()
    {
        return $this->returnProperty('JvmProcessors');
    }

    /**
     * @return string
     */
    public function getJvmMemoryFree()
    {
        return $this->returnProperty('JvmMemoryFree');
    }

    /**
     * @return string
     */
    public function getJvmMemoryTotal()
    {
        return $this->returnProperty('JvmMemoryTotal');
    }

    /**
     * @return string
     */
    public function getJvmMemoryMax()
    {
        return $this->returnProperty('JvmMemoryMax');
    }

    /**
     * @return string
     */
    public function getJvmMemoryUsed()
    {
        return $this->returnProperty('JvmMemoryUsed');
    }

    /**
     * @return string
     */
    public function getJvmJmxBootclasspath()
    {
        return $this->returnProperty('JvmJmxBootclasspath');
    }

    /**
     * @return string
     */
    public function getJvmJmxClasspath()
    {
        return $this->returnProperty('JvmJmxClasspath');
    }

    /**
     * @return array
     */
    public function getJvmJmxCommandLineArgs()
    {
        return $this->returnProperty('JvmJmxCommandLineArgs');
    }

    /**
     * @return string
     */
    public function getJvmJmxStartTime()
    {
        return $this->returnProperty('JvmJmxStartTime');
    }

    /**
     * @return int
     */
    public function getJvmJmxUpTimeMS()
    {
        return $this->returnProperty('JvmJmxUpTimeMS');
    }

    /**
     * @return string
     */
    public function getSystemName()
    {
        return $this->returnProperty('SystemName');
    }

    /**
     * @return string
     */
    public function getSystemVersion()
    {
        return $this->returnProperty('SystemVersion');
    }

    /**
     * @return string
     */
    public function getSystemArch()
    {
        return $this->returnProperty('SystemArch');
    }

    /**
     * @return float
     */
    public function getSystemLoadAverage()
    {
        return $this->returnProperty('SystemLoadAverage');
    }

    /**
     * @return string
     */
    public function getSystemUname()
    {
        return $this->returnProperty('SystemUname');
    }

    /**
     * @return string
     */
    public function getSystemUlimit()
    {
        return $this->returnProperty('SystemUlimit');
    }

    /**
     * @return string
     */
    public function getSystemUptime()
    {
        return $this->returnProperty('SystemUptime');
    }
}
