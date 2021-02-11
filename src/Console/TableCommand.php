<?php

namespace AWIS\SeqQueueDB\Console;

/**
 * Class TableCommand
 *
 * @package AWIS\SeqQueueDB\Console
 */
class TableCommand extends \Illuminate\Queue\Console\TableCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:seq-table';

    /**
     * Replace the generated migration with the job table stub.
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
            $this->files->get(__DIR__ . '/stubs/jobs.stub')
        );

        $this->files->put($path, $stub);
    }

}
