<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonModules extends Model
{
    use HasFactory;

    protected $table = 'queuetb_common_modules';
    
    protected $fillable = [
        'name',
        'queue_room_access',
    ];
}