<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'queuetb_roles'; // table name

    // Fillable fields
    protected $fillable = [
        'name',
    ];

    // If timestamps are not used, set it to false
    public $timestamps = false;

    // Define relationships if needed
    
}
