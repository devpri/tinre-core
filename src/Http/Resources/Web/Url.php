<?php

namespace Devpri\Tinre\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class Url extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'active' => $this->active,
            'long_url' => $this->long_url,
            'path' => $this->path,
            'total_clicks' => $this->total_clicks,
            'user' => new User($this->whenLoaded('user')),
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'authorized_actions' => $this->authorizedActions(),
        ];
    }
}
