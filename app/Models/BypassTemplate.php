<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BypassTemplate extends Model
{
    protected $table = 'bypass_template';

    protected $fillable = [
        'template_name',
        'bypass_url',
        'status',
        'last_modified_by',
    ];
}
