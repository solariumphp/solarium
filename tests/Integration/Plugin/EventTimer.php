<?php

namespace Solarium\Tests\Integration\Plugin;

use Solarium\Core\Event\Events;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Plugin\ParallelExecution\Event\Events as ParallelExecutionEvents;
use Symfony\Contracts\EventDispatcher\Event;

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

    /**
     * @var Event[]
     */
    protected $events = [];

    /**
     * Plugin init function.
     *
     * Register event listeners and start a timer.
     */
    protected function initPluginType(): void
    {
        $dispatcher = $this->client->getEventDispatcher();
        $dispatcher->addListener(Events::PRE_CREATE_REQUEST, $this->events['preCrReq'] = function () { $this->log('preCreateRequest'); });
        $dispatcher->addListener(Events::POST_CREATE_REQUEST, $this->events['postCrReq'] = function () { $this->log('postCreateRequest'); });
        $dispatcher->addListener(Events::PRE_EXECUTE_REQUEST, $this->events['preExReq'] = function () { $this->log('preExecuteRequest'); });
        $dispatcher->addListener(Events::POST_EXECUTE_REQUEST, $this->events['postExReq'] = function () { $this->log('postExecuteRequest'); });
        $dispatcher->addListener(Events::PRE_CREATE_RESULT, $this->events['preCrRes'] = function () { $this->log('preCreateResult'); });
        $dispatcher->addListener(Events::POST_CREATE_RESULT, $this->events['postCrRes'] = function () { $this->log('postCreateResult'); });
        $dispatcher->addListener(Events::PRE_EXECUTE, $this->events['preEx'] = function () { $this->log('preExecute'); });
        $dispatcher->addListener(Events::POST_EXECUTE, $this->events['postEx'] = function () { $this->log('postExecute'); });
        $dispatcher->addListener(Events::PRE_CREATE_QUERY, $this->events['preCrQ'] = function () { $this->log('preCreateQuery'); });
        $dispatcher->addListener(Events::POST_CREATE_QUERY, $this->events['postCrQ'] = function () { $this->log('postCreateQuery'); });
        $dispatcher->addListener(ParallelExecutionEvents::EXECUTE_START, $this->events['PE_ExStart'] = function () { $this->log('parallelExecuteStart'); });
        $dispatcher->addListener(ParallelExecutionEvents::EXECUTE_END, $this->events['PE_ExEnd'] = function () { $this->log('parallelExecuteEnd'); });

        $this->start = hrtime(true);
    }

    /**
     * Plugin cleanup function.
     *
     * Unregister event listeners.
     */
    public function deinitPlugin()
    {
        $dispatcher = $this->client->getEventDispatcher();
        $dispatcher->removeListener(Events::PRE_CREATE_REQUEST, $this->events['preCrReq']);
        $dispatcher->removeListener(Events::POST_CREATE_REQUEST, $this->events['postCrReq']);
        $dispatcher->removeListener(Events::PRE_EXECUTE_REQUEST, $this->events['preExReq']);
        $dispatcher->removeListener(Events::POST_EXECUTE_REQUEST, $this->events['postExReq']);
        $dispatcher->removeListener(Events::PRE_CREATE_RESULT, $this->events['preCrRes']);
        $dispatcher->removeListener(Events::POST_CREATE_RESULT, $this->events['postCrRes']);
        $dispatcher->removeListener(Events::PRE_EXECUTE, $this->events['preEx']);
        $dispatcher->removeListener(Events::POST_EXECUTE, $this->events['postEx']);
        $dispatcher->removeListener(Events::PRE_CREATE_QUERY, $this->events['preCrQ']);
        $dispatcher->removeListener(Events::POST_CREATE_QUERY, $this->events['postCrQ']);
        $dispatcher->removeListener(ParallelExecutionEvents::EXECUTE_START, $this->events['PE_ExStart']);
        $dispatcher->removeListener(ParallelExecutionEvents::EXECUTE_END, $this->events['PE_ExEnd']);
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
