<?php

namespace Solarium\Tests\Integration\Plugin;

use Solarium\Core\Event\Events;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Plugin\ParallelExecution\Event\Events as ParallelExecutionEvents;

/**
 * Plugin that logs the order and timing of dispatched events.
 */
class EventTimer extends AbstractPlugin
{
    /**
     * @var int|float
     */
    protected $start;

    /**
     * @var array[]
     */
    protected $log = [];

    protected function initPluginType(): void
    {
        $this->start = hrtime(true);

        $dispatcher = $this->client->getEventDispatcher();
        $dispatcher->addListener(Events::PRE_CREATE_REQUEST, function () { $this->log('preCreateRequest'); });
        $dispatcher->addListener(Events::POST_CREATE_REQUEST, function () { $this->log('postCreateRequest'); });
        $dispatcher->addListener(Events::PRE_EXECUTE_REQUEST, function () { $this->log('preExecuteRequest'); });
        $dispatcher->addListener(Events::POST_EXECUTE_REQUEST, function () { $this->log('postExecuteRequest'); });
        $dispatcher->addListener(Events::PRE_CREATE_RESULT, function () { $this->log('preCreateResult'); });
        $dispatcher->addListener(Events::POST_CREATE_RESULT, function () { $this->log('postCreateResult'); });
        $dispatcher->addListener(Events::PRE_EXECUTE, function () { $this->log('preExecute'); });
        $dispatcher->addListener(Events::POST_EXECUTE, function () { $this->log('postExecute'); });
        $dispatcher->addListener(Events::PRE_CREATE_QUERY, function () { $this->log('preCreateQuery'); });
        $dispatcher->addListener(Events::POST_CREATE_QUERY, function () { $this->log('postCreateQuery'); });
        $dispatcher->addListener(ParallelExecutionEvents::EXECUTE_START, function () { $this->log('parallelExecuteStart'); });
        $dispatcher->addListener(ParallelExecutionEvents::EXECUTE_END, function () { $this->log('parallelExecuteEnd'); });
    }

    /**
     * @param string $event
     */
    protected function log(string $event): void
    {
        $this->log[] = [
            'event' => $event,
            'time' => hrtime(true) - $this->start,
        ];
    }

    /**
     * @return array[]
     */
    public function getLog(): array
    {
        return $this->log;
    }

    /**
     * Truncate the log and start a new timer.
     */
    public function reset(): void
    {
        $this->log = [];
        $this->start = hrtime(true);
    }
}
