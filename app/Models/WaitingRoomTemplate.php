<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaitingRoomTemplate extends Model
{
    protected $table = 'waiting_room_template';

    protected $fillable = [
        'template_id',
        'html_script',
        'status',
        'last_modified_by',
    ];
}
