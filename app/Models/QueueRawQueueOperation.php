<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueRawQueueOperation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'queuetb_raw_queue_operations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status', 'queue_serial_number_id'];
}
