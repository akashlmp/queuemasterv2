<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmailNotification;
use App\Notifications\CustomResetPasswordNotification;

class QueuedbUser extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'queuetb_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_name',
        'company_address',
        'company_address2',
        'company_zip',
        'company_person_name',
        'company_person_mobile',
        'country',
        'telephonePrefix',
        'email',
        'password',
        't_c_check',
        'role',
        'status',
        'pr_user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    //protected $table = 'queuetb_users'; // Specify the correct table name
    
   

    public function createdBy()
    {
        return $this->belongsTo(QueuedbUser::class, 'pr_user_id');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmailNotification);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
}
