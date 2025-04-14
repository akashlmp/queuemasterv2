<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueRoomSetup extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'queue_room_setup'; // Corrected table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'queue_room_name',
        'queue_timezone',
        'queue_starttype',
        'queue_start',
        'queue_endtype',
        'queue_end',
        'queue_template',
        'queue_template_name',
        'queue_input_url',
        'queue_protection',
        'queue_target_offer',
        'queue_advance_setting',
        'max_traffic',
        'queue_bypass_room',
        'pre_queue',
        'pre_queue_start',
        'queue_language',
        'queue_language_default',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    public function createdByUser()
    {
        return $this->belongsTo(QueuedbUser::class, 'created_by');
    }

    public static function getAllQueueAttributes()
    {
        $queueRoomSetups = self::with('createdByUser')->get();
        $attributesArray = [];

        foreach ($queueRoomSetups as $queueRoomSetup) {
            $attributesArray[] = [
                'queue_timezone' => $queueRoomSetup->queue_timezone,
                'queue_room_name' => $queueRoomSetup->queue_room_name,
                'queue_start' => $queueRoomSetup->queue_start,
                'queue_end' => $queueRoomSetup->queue_end,
                'created_by' => [
                    'id' => $queueRoomSetup->created_by,
                    'name' => $queueRoomSetup->createdByUser ? $queueRoomSetup->createdByUser->name : null,
                ],
                'created_at' => $queueRoomSetup->created_at,
                'updated_at' => $queueRoomSetup->updated_at,
                'updated_by' => $queueRoomSetup->updated_by,
            ];
        }

        return $attributesArray;
    }
    public function user()
    {
        return $this->belongsTo(QueuedbUser::class, 'updated_by');
    }
    
}
