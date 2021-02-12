<?php


namespace AWIS\SeqQueueDB;


/**
 * Trait SequnceEneityQueueble
 *
 * @package AWIS\SeqQueueDB
 */
trait SequnceEneityQueueble
{

    /**
     * Name subject entity sequnce queue
     * if return null - not execute sequnce jobs
     *
     * @return string|null
     */
    public function sequenceEntity() : ?string
    {
        return static::class;
    }

    /**
     * Whether the execution of subsequent tasks within the given entity of sequential task execution is required if
     * this task completed with an error
     *
     * @return bool
     */
    public function isStopQueueAfterExecute() : bool
    {
        return false;
    }

}
