<?php

namespace AWIS\SeqQueueDB\Providers;


use AWIS\SeqQueueDB\Console\FailedTableCommand;
use AWIS\SeqQueueDB\Console\TableCommand;
use AWIS\SeqQueueDB\DatabaseConnector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Queue;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;


/**
 * Class LaravelServiceProvider
 *
 * @package AWIS\SeqQueueDB\Providers
 */
class LaravelServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->initListenerPayloadUsing();

        $this->addConfig();

        if ($this->app->runningInConsole()) {
            $this->commands([
                FailedTableCommand::class,
                TableCommand::class,
            ]);
        }
    }

    /**
     * Boot
     *
     * @return void
     */
    public function boot() : void
    {
        /** @var QueueManager $manager */
        $manager = $this->app['queue'];

        $this->addSeqDatabaseConnector($manager);
    }

    /**
     * Add connector in QueueManager
     *
     * @param QueueManager $manager
     */
    protected function addSeqDatabaseConnector(QueueManager $manager) : void
    {
        $manager->addConnector('seq_database', function () {
            return new DatabaseConnector($this->app['db']);
        });
    }

    /**
     * Registering a listener for the payload job creation event
     *
     * @return void
     */
    protected function initListenerPayloadUsing() : void
    {
        Queue::createPayloadUsing(function ($connection, $queue, $payload) {
            /** @var ShouldQueue|mixed $job */
            $job = Arr::get($payload, 'data.command');

            return [
                'seq_entity'  => method_exists($job, 'sequenceEntity') ? $job->sequenceEntity() : null,
                'seq_is_stop' => method_exists($job, 'isStopQueueAfterExecute') ? $job->isStopQueueAfterExecute() : false,
            ];
        });
    }

    /**
     * Merge config connector from laravel config/queue.php
     *
     * @return void
     */
    protected function addConfig() : void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/queue.php',
            'queue.connections'
        );
    }

}
