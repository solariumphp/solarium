<?php

namespace Solarium\Tests\QueryType\Server;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\AbstractRequestBuilder;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Server\AbstractServerQuery;
use Solarium\QueryType\Server\Query\Action\AbstractAction;

class ServerQueryTest extends TestCase
{
    /**
     * @var ServerQuery
     */
    protected $query;

    public function setUp(): void
    {
        $this->query = new ServerQuery();
    }

    public function testCreateAction()
    {
        $action = $this->query->createAction(ServerQuery::ACTION_DUMMY);
        $this->assertInstanceOf(DummyAction::class, $action);
    }

    public function testCreateUnknownAction()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Action unknown: UNKNOWN');
        $this->query->createAction('UNKNOWN');
    }

    public function testSetAction()
    {
        $action = new DummyAction();
        $this->query->setAction($action);
        $this->assertSame($action, $this->query->getAction());
    }

    public function testGetResultClass()
    {
        $action = new DummyAction();
        $this->query->setAction($action);
        $this->assertSame($action->getResultClass(), $this->query->getResultClass());
    }
}

class ServerQuery extends AbstractServerQuery
{
    /**
     * Dummy action.
     */
    const ACTION_DUMMY = 'DUMMY';

    /**
     * Action types.
     *
     * @var array
     */
    protected $actionTypes = [
        self::ACTION_DUMMY => DummyAction::class,
    ];

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'server';
    }

    /**
     * @return RequestBuilderInterface
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new ServerRequestBuilder();
    }

    /**
     * @return ResponseParserInterface|null
     */
    public function getResponseParser(): ?ResponseParserInterface
    {
        return null;
    }
}

class DummyAction extends AbstractAction
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return ServerQuery::ACTION_DUMMY;
    }
}

class ServerRequestBuilder extends AbstractRequestBuilder
{
}
