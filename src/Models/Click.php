<?php

namespace Devpri\Tinre\Models;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['url_id', 'country', 'region', 'city', 'device_type', 'device_brand', 'device_model', 'user_agent', 'os', 'browser', 'referer', 'referer_host', 'created_at'];

    public function url()
    {
        return $this->belongsTo('Devpri\Tinre\Models\Url');
    }
}
