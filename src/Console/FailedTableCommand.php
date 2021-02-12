<?php

namespace AWIS\SeqQueueDB\Console;

/**
 * Class FailedTableCommand
 *
 * @package AWIS\SeqQueueDB\Console
 */
class FailedTableCommand extends \Illuminate\Queue\Console\FailedTableCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:failed-seq-table';

    /**
     * Create a base migration file for the table.
     *
     * @param  string  $table
     * @return string
     */
    protected function createBaseMigration($table = 'failed_jobs')
    {
        return $this->laravel['migration.creator']->create(
            'alter_'.$table.'_table', $this->laravel->databasePath().'/migrations'
        );
    }

    /**
     * Replace the generated migration with the failed job table stub.
     *
     * @param string $path
     * @param string $table
     * @param string $tableClassName
     *
     * @return void
     */
    protected function replaceMigration($path, $table, $tableClassName)
    {
        $stub = str_replace(
            ['{{table}}', '{{tableClassName}}'],
            [$table, $tableClassName],
            $this->files->get(__DIR__ . '/stubs/failed_jobs.stub')
        );

        $this->files->put($path, $stub);
    }

}
