<?php

namespace AWIS\SeqQueueDB\Failed;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

/**
 * Class DatabaseUuidFailedJobProvider
 *
 * @package AWIS\SeqQueueDB\Failed
 */
class DatabaseUuidFailedJobProvider extends \Illuminate\Queue\Failed\DatabaseUuidFailedJobProvider
{

    /**
     * Log a failed job into storage.
     *
     * @param string $connection
     * @param string $queue
     * @param string $payload
     * @param \Throwable $exception
     *
     * @return int|null
     */
    public function log($connection, $queue, $payload, $exception)
    {
        $info = json_decode($payload, true);

        $this->getTable()->insert([
            'uuid'        => $uuid = $info['uuid'],
            'connection'  => $connection,
            'queue'       => $queue,
            'seq_entity'  => Arr::get($info, 'seq_entity'),
            'seq_is_stop' => Arr::get($info, 'seq_is_stop'),
            'payload'     => $payload,
            'exception'   => (string) $exception,
            'failed_at'   => Date::now(),
        ]);

        return $uuid;
    }

}
