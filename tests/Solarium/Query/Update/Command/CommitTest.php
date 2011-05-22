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

class Solarium_Query_Update_Command_CommitTest extends PHPUnit_Framework_TestCase
{
    protected $_command;

    public function setUp()
    {
        $this->_command = new Solarium_Query_Update_Command_Commit;
    }

    public function testGetType()
    {
        $this->assertEquals(
            Solarium_Query_Update::COMMAND_COMMIT,
            $this->_command->getType()
        );
    }

    public function testConfigMode()
    {
        $options = array(
            'waitflush' => true,
            'waitsearcher' => false,
            'expungedeletes' => true,
        );

        $command = new Solarium_Query_Update_Command_Commit($options);

        $this->assertEquals(
            true,
            $command->getWaitFlush()
        );

        $this->assertEquals(
            false,
            $command->getWaitSearcher()
        );

        $this->assertEquals(
            true,
            $command->getExpungeDeletes()
        );
    }

    public function testGetAndSetWaitFlush()
    {
        $this->_command->setWaitFlush(false);
        $this->assertEquals(
            false,
            $this->_command->getWaitFlush()
        );
    }

    public function testGetAndSetWaitSearcher()
    {
        $this->_command->setWaitSearcher(false);
        $this->assertEquals(
            false,
            $this->_command->getWaitSearcher()
        );
    }

    public function testGetAndSetExpungeDeletes()
    {
        $this->_command->setExpungeDeletes(true);
        $this->assertEquals(
            true,
            $this->_command->getExpungeDeletes()
        );
    }
}
