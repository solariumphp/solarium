<?php
/**
 * Copyright 2012 Marc Morera. All rights reserved.
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

namespace Solarium\Tests\QueryType\Select\RequestBuilder\Component;

use Solarium\QueryType\Select\RequestBuilder\Component\EdisMax as RequestBuilder;
use Solarium\QueryType\Select\Query\Component\EdisMax as Component;
use Solarium\Core\Client\Request;

class EDisMaxTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder;
        $request = new Request();

        $component = new Component();
        $component->setQueryParser('dummyparser');
        $component->setQueryAlternative('test');
        $component->setQueryFields('content,name');
        $component->setMinimumMatch('75%');
        $component->setPhraseFields('content,description');
        $component->setPhraseSlop(1);
        $component->setPhraseBigramFields('content,description,category');
        $component->setPhraseBigramSlop(4);
        $component->setPhraseTrigramFields('content2,date,subcategory');
        $component->setPhraseTrigramSlop(3);
        $component->setQueryPhraseSlop(2);
        $component->setTie(0.5);
        $component->setBoostQuery('cat:1');
        $component->setBoostFunctions('functionX(price)');
        $component->setBoostFunctionsMult('functionX(date)');
        $component->setUserFields('title *_s');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            array(
                'defType' => 'dummyparser',
                'q.alt' => 'test',
                'qf' => 'content,name',
                'mm' => '75%',
                'pf' => 'content,description',
                'ps' => 1,
                'pf2' => 'content,description,category',
                'ps2' => 4,
                'pf3' => 'content2,date,subcategory',
                'ps3' => 3,
                'qs' => 2,
                'tie' => 0.5,
                'bq' => 'cat:1',
                'bf' => 'functionX(price)',
                'boost' => 'functionX(date)',
                'uf' => 'title *_s',
            ),
            $request->getParams()
        );

    }
}
