<?php

namespace AWIS\SeqQueueDB;



/**
 * Class DatabaseConnector
 *
 * @package AWIS\SeqQueueDB
 */
class DatabaseConnector extends \Illuminate\Queue\Connectors\DatabaseConnector
{

    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new DatabaseQueue(
            $this->connections->connection($config['connection']),
            $config['table'],
            $config['queue'],
            $config['retry_after'] ?? 60,
            $config['after_commit'] ?? false
        );
    }

}
