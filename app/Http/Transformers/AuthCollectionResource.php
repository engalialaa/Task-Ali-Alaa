<?php

namespace App\Http\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'token'=>$this->token??''
        ];
    }
}
