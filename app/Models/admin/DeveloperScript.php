<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperScript extends Model
{
    use HasFactory;

    protected $table = 'queuetb_developers_script';

    protected $fillable = [
        'script',
    ];
}
