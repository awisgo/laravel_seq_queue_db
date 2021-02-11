<?php

namespace AWIS\SeqQueueDB\Providers;


use Illuminate\Queue\Queue;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use AWIS\SeqQueueDB\Console\FailedTableCommand;
use AWIS\SeqQueueDB\Console\TableCommand;
use AWIS\SeqQueueDB\DatabaseConnector;
use AWIS\SeqQueueDB\SeqShouldQueue;
use AWIS\SeqQueueDB\SeqStopQueueAfterExecute;


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
        Queue::createPayloadUsing(function ($connection, $queue, $payload) {
            /** @var SeqShouldQueue|mixed $job */
            $job = Arr::get($payload, 'data.command');

            return [
                'seq_entity'  => $job instanceof SeqShouldQueue ? $job->entity() : null,
                'seq_is_stop' => $job instanceof SeqStopQueueAfterExecute,
            ];
        });
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

        if ($this->app->runningInConsole()) {
            $this->commands([
                FailedTableCommand::class,
                TableCommand::class,
            ]);
        }
    }

    /**
     * @param QueueManager $manager
     */
    protected function addSeqDatabaseConnector(QueueManager $manager) : void
    {
        $manager->addConnector('seq_database', function () {
            return new DatabaseConnector($this->app['db']);
        });
    }

}
