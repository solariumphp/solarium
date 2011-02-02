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

class Solarium_Query_UpdateTest extends PHPUnit_Framework_TestCase
{

    protected $_query;

    public function setUp()
    {
        $this->_query = new Solarium_Query_Update;
    }

    public function testAddWithoutKey()
    {
        $command = new Solarium_Query_Update_Command_Rollback;
        $this->_query->add(null, $command);

        $this->assertEquals(
            array($command),
            $this->_query->getCommands()
        );
    }
    
    public function testAddWithKey()
    {
        $rollback = new Solarium_Query_Update_Command_Rollback;
        $this->_query->add('rb', $rollback);

        $commit = new Solarium_Query_Update_Command_Commit;
        $this->_query->add('cm', $commit);

        $this->assertEquals(
            array('rb' => $rollback, 'cm' => $commit),
            $this->_query->getCommands()
        );
    }

    public function testRemove()
    {
        $rollback = new Solarium_Query_Update_Command_Rollback;
        $this->_query->add('rb', $rollback);

        $commit = new Solarium_Query_Update_Command_Commit;
        $this->_query->add('cm', $commit);
        
        $this->_query->remove('rb');

        $this->assertEquals(
            array('cm' => $commit),
            $this->_query->getCommands()
        );
    }

    public function testRemoveInvalidKey()
    {
        $rollback = new Solarium_Query_Update_Command_Rollback;
        $this->_query->add('rb', $rollback);

        $commit = new Solarium_Query_Update_Command_Commit;
        $this->_query->add('cm', $commit);

        $this->_query->remove('invalidkey'); //should silently ignore

        $this->assertEquals(
            array('rb' => $rollback, 'cm' => $commit),
            $this->_query->getCommands()
        );
    }

    public function testAddRollback()
    {
        $this->_query->addRollback();
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update_Command::ROLLBACK,
            $commands[0]->getType()
        );
    }

    public function testAddDeleteQuery()
    {
        $this->_query->addDeleteQuery('*:*');
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update_Command::DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array('*:*'),
            $commands[0]->getQueries()
        );
    }

    public function testAddDeleteQueries()
    {
        $this->_query->addDeleteQueries(array('id:1','id:2'));
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update_Command::DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array('id:1','id:2'),
            $commands[0]->getQueries()
        );
    }

    public function testAddDeleteById()
    {
        $this->_query->addDeleteById(1);
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update_Command::DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array(1),
            $commands[0]->getIds()
        );
    }

    public function testAddDeleteByIds()
    {
        $this->_query->addDeleteByIds(array(1,2));
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update_Command::DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array(1,2),
            $commands[0]->getIds()
        );
    }

    public function testAddDocument()
    {
        $doc = new Solarium_Document_ReadWrite(array('id' => 1));

        $this->_query->addDocument($doc);
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update_Command::ADD,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array($doc),
            $commands[0]->getDocuments()
        );
    }

    public function testAddDocuments()
    {
        $doc1 = new Solarium_Document_ReadWrite(array('id' => 1));
        $doc2 = new Solarium_Document_ReadWrite(array('id' => 1));

        $this->_query->addDocuments(array($doc1,$doc2), true, 100);
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update_Command::ADD,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array($doc1, $doc2),
            $commands[0]->getDocuments()
        );

        $this->assertEquals(
            true,
            $commands[0]->getOverwrite()
        );

        $this->assertEquals(
            100,
            $commands[0]->getCommitWithin()
        );
    }

    public function testAddCommit()
    {
        $this->_query->addCommit(true, false, true);
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update_Command::COMMIT,
            $commands[0]->getType()
        );

        $this->assertEquals(
            true,
            $commands[0]->getWaitFlush()
        );

        $this->assertEquals(
            false,
            $commands[0]->getWaitSearcher()
        );

        $this->assertEquals(
            true,
            $commands[0]->getExpungeDeletes()
        );
    }

    public function testAddOptimize()
    {
        $this->_query->addOptimize(true, false, 10);
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update_Command::OPTIMIZE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            true,
            $commands[0]->getWaitFlush()
        );

        $this->assertEquals(
            false,
            $commands[0]->getWaitSearcher()
        );

        $this->assertEquals(
            10,
            $commands[0]->getMaxSegments()
        );
    }

}
