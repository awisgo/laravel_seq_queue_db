<?php


namespace AWIS\SeqQueueDB\Providers;


use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use AWIS\SeqQueueDB\Failed\DatabaseUuidFailedJobProvider;

/**
 * Class RegisterFailedJobServiceProvider
 *
 * @package AWIS\SeqQueueDB\Providers
 */
class RegisterFailedJobServiceProvider extends ServiceProvider implements DeferrableProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->registerFailedJobServices();
    }

    /**
     * Register the failed job services.
     *
     * @return void
     */
    protected function registerFailedJobServices() : void
    {
        $failer = $this->app['queue.failer'];

        $this->app->singleton('queue.failer', function ($app) use ($failer) {
            $config = $app['config']['queue.failed'];

            if (isset($config['driver']) && $config['driver'] === 'database-uuids') {
                return $this->databaseUuidFailedJobProvider($config);
            } else {
                return $failer;
            }
        });
    }

    /**
     * Create a new database failed job provider that uses UUIDs as IDs.
     *
     * @param array $config
     *
     * @return \Illuminate\Queue\Failed\DatabaseUuidFailedJobProvider
     */
    protected function databaseUuidFailedJobProvider($config) : DatabaseUuidFailedJobProvider
    {
        return new DatabaseUuidFailedJobProvider(
            $this->app['db'], $config['database'], $config['table']
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() : array
    {
        return [
            'queue.failer',
        ];
    }

}
