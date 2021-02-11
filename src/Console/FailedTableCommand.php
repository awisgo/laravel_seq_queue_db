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
