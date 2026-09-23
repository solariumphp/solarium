<?php

namespace Solarium\Tests\QueryType\Server\Metrics;

use Butschster\Prometheus\Ast\SchemaNode;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Server\Metrics\Query;
use Solarium\QueryType\Server\Metrics\ResponseParser;
use Solarium\QueryType\Server\Metrics\Result;

class ResponseParserTest extends TestCase
{
    public function testParsePrometheus(): void
    {
        $data =
            <<<'PROMETHEUS'
            # HELP solr_core_disk_space_megabytes Solr core disk space metrics
            # TYPE solr_core_disk_space_megabytes gauge
            solr_core_disk_space_megabytes{category="CORE",core="techproducts",otel_scope_name="org.apache.solr",type="total_space"} 1031018.42578125
            solr_core_disk_space_megabytes{category="CORE",core="techproducts",otel_scope_name="org.apache.solr",type="usable_space"} 898917.8359375
            # HELP solr_core_index_size_megabytes Index size for a Solr core
            # TYPE solr_core_index_size_megabytes gauge
            solr_core_index_size_megabytes{category="CORE",core="techproducts",otel_scope_name="org.apache.solr"} 0.0427398681640625
            # HELP solr_core_ref_count The current number of active references to a Solr core
            # TYPE solr_core_ref_count gauge
            solr_core_ref_count{category="CORE",core="techproducts",otel_scope_name="org.apache.solr"} 1.0
            # HELP solr_core_segments Number of segments in a Solr core
            # TYPE solr_core_segments gauge
            solr_core_segments{category="CORE",core="techproducts",otel_scope_name="org.apache.solr"} 3.0
            
            PROMETHEUS;

        $response = new Response($data, ['HTTP/1.1 200 OK']);
        $result = new Result(new Query(['responsewriter' => Query::WT_PROMETHEUS]), $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $metricSet = $parsed['metricSet'];
        $this->assertInstanceOf(SchemaNode::class, $metricSet);

        $metricFamily = $metricSet->getMetrics()['solr_core_index_size_megabytes'];
        $this->assertSame('solr_core_index_size_megabytes', $metricFamily->name);
        $this->assertSame('Index size for a Solr core', $metricFamily->description);
        $this->assertSame('gauge', $metricFamily->type);
        $this->assertNull($metricFamily->unit);
        $this->assertSame('core', $metricFamily->metrics[0]->labels[1]->name);
        $this->assertSame('techproducts', $metricFamily->metrics[0]->labels[1]->value);
        $this->assertSame(0.0427398681640625, $metricFamily->metrics[0]->value);

        $this->assertNull($metricSet->eof);
    }

    public function testParseEmptyResponsePrometheus(): void
    {
        $data = '';

        $response = new Response($data, ['HTTP/1.1 200 OK']);
        $result = new Result(new Query(['responsewriter' => Query::WT_PROMETHEUS]), $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $this->assertCount(0, $parsed['metricSet']->getMetrics());
        $this->assertNull($parsed['metricSet']->eof);
    }

    public function testParseOpenmetrics(): void
    {
        $data =
            <<<'OPENMETRICS'
            # TYPE solr_core_disk_space_megabytes gauge
            # UNIT solr_core_disk_space_megabytes megabytes
            # HELP solr_core_disk_space_megabytes Solr core disk space metrics
            solr_core_disk_space_megabytes{category="CORE",core="techproducts",otel_scope_name="org.apache.solr",type="total_space"} 1031018.42578125
            solr_core_disk_space_megabytes{category="CORE",core="techproducts",otel_scope_name="org.apache.solr",type="usable_space"} 898917.8359375
            # TYPE solr_core_index_size_megabytes gauge
            # UNIT solr_core_index_size_megabytes megabytes
            # HELP solr_core_index_size_megabytes Index size for a Solr core
            solr_core_index_size_megabytes{category="CORE",core="techproducts",otel_scope_name="org.apache.solr"} 0.0427398681640625
            # TYPE solr_core_ref_count gauge
            # HELP solr_core_ref_count The current number of active references to a Solr core
            solr_core_ref_count{category="CORE",core="techproducts",otel_scope_name="org.apache.solr"} 1.0
            # TYPE solr_core_segments gauge
            # HELP solr_core_segments Number of segments in a Solr core
            solr_core_segments{category="CORE",core="techproducts",otel_scope_name="org.apache.solr"} 3.0
            # EOF
            
            OPENMETRICS;

        $response = new Response($data, ['HTTP/1.1 200 OK']);
        $result = new Result(new Query(['responsewriter' => Query::WT_OPENMETRICS]), $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $metricSet = $parsed['metricSet'];
        $this->assertInstanceOf(SchemaNode::class, $metricSet);

        $metricFamily = $metricSet->getMetrics()['solr_core_index_size_megabytes'];
        $this->assertSame('solr_core_index_size_megabytes', $metricFamily->name);
        $this->assertSame('Index size for a Solr core', $metricFamily->description);
        $this->assertSame('gauge', $metricFamily->type);
        $this->assertSame('megabytes', $metricFamily->unit);
        $this->assertSame('core', $metricFamily->metrics[0]->labels[1]->name);
        $this->assertSame('techproducts', $metricFamily->metrics[0]->labels[1]->value);
        $this->assertSame(0.0427398681640625, $metricFamily->metrics[0]->value);

        $this->assertTrue($metricSet->eof);
    }

    public function testParseEmptyResponseOpenmetrics(): void
    {
        $data =
            <<<'OPENMETRICS'
            # EOF
            
            OPENMETRICS;

        $response = new Response($data, ['HTTP/1.1 200 OK']);
        $result = new Result(new Query(['responsewriter' => Query::WT_OPENMETRICS]), $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $this->assertCount(0, $parsed['metricSet']->getMetrics());
        $this->assertTrue($parsed['metricSet']->eof);
    }
}
