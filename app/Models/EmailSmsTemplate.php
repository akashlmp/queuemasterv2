<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSmsTemplate extends Model
{
    protected $table = 'queuetb_email_sms'; 

    protected $primaryKey = 'id';

    protected $fillable = [
        'email_template_name',
        'sms_template_name',
        'html_content',
        'status',
        'queue_room_id',
        'last_modified_by'
    ];
    public function queueRoom()
    {
        return $this->belongsTo(QueueRoomSetup::class, 'queue_room_id', 'id');
    }
    // Define the relationship with User model for last modified by user
    public function lastModifiedByUser()
    {
        return $this->belongsTo(QueuedbUser::class, 'last_modified_by', 'id');
    }
}
