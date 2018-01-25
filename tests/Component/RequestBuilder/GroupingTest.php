<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Grouping as Component;
use Solarium\Component\RequestBuilder\Grouping as RequestBuilder;
use Solarium\Core\Client\Request;

class GroupingTest extends TestCase
{
    public function testBuildComponent()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->setFields(['fieldA', 'fieldB']);
        $component->setQueries(['cat:1', 'cat:2']);
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
            [
                'group' => 'true',
                'group.field' => ['fieldA', 'fieldB'],
                'group.query' => ['cat:1', 'cat:2'],
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
            ],
            $request->getParams()
        );
    }
}
