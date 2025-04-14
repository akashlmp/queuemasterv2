<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionAccess extends Model
{
    protected $table = 'queuetb_permission_access';
    
    protected $fillable = [
        'module_id',
        'role_id',
        'user_id',
        'has_permission',
    ];
}
