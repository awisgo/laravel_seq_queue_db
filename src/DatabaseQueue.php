<?php

namespace AWIS\SeqQueueDB;


use Illuminate\Database\Query\Builder;
use Illuminate\Queue\Jobs\DatabaseJobRecord;
use Illuminate\Support\Arr;

/**
 * Class DatabaseQueue
 *
 * @package AWIS\SeqQueueDB
 */
class DatabaseQueue extends \Illuminate\Queue\DatabaseQueue
{

    /**
     * Create an array to insert for the given job.
     *
     * @param string|null $queue
     * @param string $payload
     * @param int $availableAt
     * @param int $attempts
     *
     * @return array
     */
    protected function buildDatabaseRecord($queue, $payload, $availableAt, $attempts = 0) : array
    {
        $payload = json_decode($payload, true);

        if (Arr::get($payload, 'seq_is_stop')) {
            if ( ! Arr::get($payload, 'available_at')) {
                $payload['available_at'] = $availableAt;
            }
            if ($attempts == 0) {
                $availableAt = $payload['available_at'];
            }
        }

        return array_merge(
            parent::buildDatabaseRecord($queue, json_encode($payload), $availableAt, $attempts),
            [
                'seq_entity'  => Arr::get($payload, 'seq_entity'),
                'seq_is_stop' => Arr::get($payload, 'seq_is_stop'),
            ],
        );
    }

    /**
     * Get the next available job for the queue.
     *
     * @param string|null $queue
     *
     * @return \Illuminate\Queue\Jobs\DatabaseJobRecord|null
     */
    protected function getNextAvailableJob($queue)
    {
        $job = $this->database->table($this->table)
            ->lock($this->getLockForPopping())
            ->where('queue', $this->getQueue($queue))
            ->where(function (Builder $query) {
                $this->isAvailable($query);
                $this->isReservedButExpired($query);
            })
            ->where(function (Builder $query) {
                $query->whereNull('seq_entity');
                $query->orWhere(function (Builder $query) {
                    $this->isNotExistSeqJob($query);
                    $this->isNotExistFeildJob($query);
                });
            })
            ->orderBy('available_at', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        return $job ? new DatabaseJobRecord((object) $job) : null;
    }

    /**
     * @param Builder $query
     */
    protected function isNotExistSeqJob(Builder $query) : void
    {
        $query->whereNotExists(function (Builder $query) {
            $query->from('jobs', 'jobs2')
                ->whereColumn('jobs.seq_entity', 'jobs2.seq_entity')
                ->whereNotNull('jobs2.reserved_at');
        });
    }

    /**
     * @param Builder $query
     */
    protected function isNotExistFeildJob(Builder $query) : void
    {
        $query->whereNotExists(function (Builder $query) {
            $query->from('failed_jobs')
                ->whereColumn('jobs.seq_entity', 'failed_jobs.seq_entity')
                ->where('failed_jobs.seq_is_stop', '=', true);
        });
    }

}
