<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueRoomTemplate extends Model
{
    protected $table = 'queue_room_template';

    protected $fillable = [
        'template_name',
        'input_url',
        'protection_level',
        'is_advance_setting',
        'advance_setting_rules',
        'last_modified_by'
    ];
}
