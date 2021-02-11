<?php


namespace AWIS\SeqQueueDB;


use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Interface SeqShouldQueue
 *
 * @package AWIS\SeqQueueDB
 */
interface SeqShouldQueue extends ShouldQueue
{

    /**
     * @return string
     */
    public function entity() : string;

}
