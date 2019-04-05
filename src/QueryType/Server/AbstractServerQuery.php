<?php

namespace Solarium\QueryType\Server;

use Solarium\Core\Query\AbstractQuery;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Server\Query\Action\ActionInterface;

/**
 * Base class for all server queries, these query are not executed in the context of a collection or a core.
 */
abstract class AbstractServerQuery extends AbstractQuery implements ActionInterface
{
    /**
     * Action that should be performed on the core admin api.
     *
     * @var ActionInterface
     */
    protected $action;

    /**
     * Action types.
     *
     * @var array
     */
    protected $actionTypes = [];

    /**
     * Create a command instance.
     *
     * @param string $type
     * @param mixed  $options
     *
     * @throws InvalidArgumentException
     *
     * @return ActionInterface
     */
    public function createAction($type, $options = null): ActionInterface
    {
        if (!isset($this->actionTypes[$type])) {
            throw new InvalidArgumentException('Action unknown: '.$type);
        }

        $class = $this->actionTypes[$type];

        return new $class($options);
    }

    /**
     * @param ActionInterface $action
     */
    public function setAction(ActionInterface $action)
    {
        $this->action = $action;
    }

    /**
     * Get the active action.
     *
     * @return ActionInterface
     */
    public function getAction(): ActionInterface
    {
        return $this->action;
    }

    /**
     * Returns the result class.
     *
     * @return string
     */
    public function getResultClass(): string
    {
        return $this->getAction()->getResultClass();
    }
}
