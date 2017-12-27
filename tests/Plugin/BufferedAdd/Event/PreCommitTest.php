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
 */

namespace Solarium\Tests\Plugin\BufferedAdd\Event;

use Solarium\Plugin\BufferedAdd\Event\PreCommit;

class PreCommitTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndGetters()
    {
        $buffer = array(1, 2, 3);
        $overwrite = true;
        $softCommit = false;
        $waitSearcher = true;
        $expungeDeletes = false;

        $event = new PreCommit($buffer, $overwrite, $softCommit, $waitSearcher, $expungeDeletes);

        $this->assertEquals($buffer, $event->getBuffer());
        $this->assertEquals($overwrite, $event->getOverwrite());
        $this->assertEquals($softCommit, $event->getSoftCommit());
        $this->assertEquals($waitSearcher, $event->getWaitSearcher());
        $this->assertEquals($expungeDeletes, $event->getExpungeDeletes());

        return $event;
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
     */
    public function testSetAndGetBuffer($event)
    {
        $buffer = array(4, 5, 6);
        $event->setBuffer($buffer);
        $this->assertEquals($buffer, $event->getBuffer());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
     */
    public function testSetAndGetExpungeDeletes($event)
    {
        $expungeDeletes = true;
        $event->setExpungeDeletes($expungeDeletes);
        $this->assertEquals($expungeDeletes, $event->getExpungeDeletes());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
     */
    public function testSetAndGetOverwrite($event)
    {
        $overwrite = false;
        $event->setOverwrite($overwrite);
        $this->assertEquals($overwrite, $event->getOverwrite());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
     */
    public function testSetAndGetSoftCommit($event)
    {
        $softCommit = true;
        $event->setSoftCommit($softCommit);
        $this->assertEquals($softCommit, $event->getSoftCommit());
    }

    /**
     * @depends testConstructorAndGetters
     *
     * @param PreCommit $event
     */
    public function testSetAndGetWaitSearcher($event)
    {
        $waitSearcher = false;
        $event->setWaitSearcher($waitSearcher);
        $this->assertEquals($waitSearcher, $event->getWaitSearcher());
    }
}
