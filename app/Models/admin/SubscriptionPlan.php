<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'queuetb_subscription_plan';

    protected $fillable = [
        'number_of_queue_room',
        'monitor_queue_room',
        'in_line_editing_queue_room',
        'sub_accounts',
        'maximum_queue_room',
        'maximum_traffic',
        'maximum_sub_accounts' ,
        'staff_access_management',
        'setup_bypass',
        'setup_pre_queue',
        'setup_sms',
        'setup_email',
        'package_name',
        'price',
        'package_desc',
        'featured_plan',
        'highlight_feature',     
        // Add other fillable columns here
    ];
}
