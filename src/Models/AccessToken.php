<?php

namespace Devpri\Tinre\Models;

use Devpri\Tinre\Traits\AuthorizedActions;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use AuthorizedActions;

    protected $actions = ['viewAny', 'view', 'create', 'update', 'delete'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'token', 'permissions'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'permissions' => 'array',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo('Devpri\Tinre\Models\User');
    }

    public static function findToken($token)
    {
        return static::where('token', hash('sha256', $token))->first();
    }
}
