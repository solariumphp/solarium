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

namespace Solarium\Tests\QueryType\Select\RequestBuilder\Component;

use Solarium\QueryType\Select\RequestBuilder\Component\Grouping as RequestBuilder;
use Solarium\QueryType\Select\Query\Component\Grouping as Component;
use Solarium\Core\Client\Request;

class GroupingTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder;
        $request = new Request();

        $component = new Component();
        $component->setFields(array('fieldA', 'fieldB'));
        $component->setQueries(array('cat:1', 'cat:2'));
        $component->setLimit(12);
        $component->setOffset(2);
        $component->setSort('score desc');
        $component->setMainResult(true);
        $component->setNumberOfGroups(false);
        $component->setCachePercentage(50);
        $component->setTruncate(true);
        $component->setFunction('log(foo)');
        $component->setFacet(true);
        $component->setFormat('grouped');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            array(
                'group' => 'true',
                'group.field' => array('fieldA', 'fieldB'),
                'group.query' => array('cat:1', 'cat:2'),
                'group.limit' => 12,
                'group.offset' => 2,
                'group.sort' => 'score desc',
                'group.main' => 'true',
                'group.ngroups' => 'false',
                'group.cache.percent' => 50,
                'group.truncate' => 'true',
                'group.func' => 'log(foo)',
                'group.facet' => 'true',
                'group.format' => 'grouped',
            ),
            $request->getParams()
        );

    }
}
