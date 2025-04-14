<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailNotice extends Model
{
    use HasFactory;

    protected $table = 'queuetb_email_notice'; // table name

    // Fillable fields
    protected $fillable = [
        'email_template',
        'email_template_name',
        'status',
    ];

    // If timestamps are not used, set it to false
    public $timestamps = false;

    // Define relationships if needed

    public function user()
    {
        return $this->belongsTo(QueuedbUser::class, 'user_id');
    }
    
}
