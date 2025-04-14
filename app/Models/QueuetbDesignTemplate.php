<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueuetbDesignTemplate extends Model
{
    protected $table = 'queuetb_design_template';

    protected $fillable = [
        'template_name',
        'languages',
        'updated_at',
        'last_modified_by'
    ];

    public $timestamps = false;
}
