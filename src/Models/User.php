<?php

namespace Devpri\Tinre\Models;

use Devpri\Tinre\Notifications\EmailVerificationNotification;
use Devpri\Tinre\Notifications\ResetPasswordNotification;
use Devpri\Tinre\Traits\AuthorizedActions;
use Devpri\Tinre\Traits\HasApiTokens;
use Devpri\Tinre\Traits\HasPermissions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, AuthorizedActions, HasPermissions, HasApiTokens;

    protected $actions = ['viewAny', 'view', 'create', 'update', 'updateOwn', 'changeEmail', 'delete'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'active', 'name', 'email', 'role', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification());
    }

    public function urls()
    {
        return $this->hasMany('Devpri\Tinre\Models\Url');
    }
}
