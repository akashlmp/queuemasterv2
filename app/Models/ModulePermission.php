<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePermission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'queuetb_modules';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'status'
    ];
}
