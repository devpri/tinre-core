<?php

namespace Devpri\Tinre\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class AccessToken extends JsonResource
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
            'name' => $this->name,
            'permissions' => $this->permissions,
            'user' => new User($this->whenLoaded('user')),
            'user_id' => $this->user_id,
            'last_used_at' => $this->last_used_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'plain_text_token' => $this->when($this->plain_text_token, $this->plain_text_token),
            'authorized_actions' => $this->authorizedActions(),
        ];
    }
}
