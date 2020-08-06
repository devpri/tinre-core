<?php

namespace Devpri\Tinre\Models;

use Devpri\Tinre\Traits\AuthorizedActions;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use AuthorizedActions;

    protected $actions = ['viewAny', 'view', 'create', 'update', 'delete'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['path', 'full_url', 'user_id'];

    public function user()
    {
        return $this->belongsTo('Devpri\Tinre\Models\User');
    }

    public function clicks()
    {
        return $this->hasMany('Devpri\Tinre\Models\Click');
    }
}
