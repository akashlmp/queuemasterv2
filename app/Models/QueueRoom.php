<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueRoom extends Model
{
    protected $table = 'queuetb_queue_room';

    protected $fillable = [
        'queue_room_name',
        'queue_room_type',
        'queue_room_icon',
        'queue_timezone',
        'is_started',
        'start_date',
        'start_time',
        'is_ended',
        'end_date',
        'end_time',
        'queue_room_template_id',
        'target_url',
        'max_traffic_visitor',
        'enable_bypass',
        'bypass_template_id',
        'is_prequeue',
        'prequeue_starttime',
        'start_time_epoch',
        'end_time_epoch',
        'queue_room_design_tempid',
        'sms_notice_tempid',
        'email_notice_tempid',
        'last_modified_by',
        'parent_user_id',
        'is_draft'
    ];
}
