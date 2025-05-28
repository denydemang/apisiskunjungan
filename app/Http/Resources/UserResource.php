<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    protected $msg;
    protected $token;
    public function __construct($resource, $msg , $token = null)
    {
        parent::__construct($resource);
        $this->msg =$msg;
        $this->token =$token;
    }
    public function toArray($request) :array
    {


        return [
            "data" => [
                "id" => $this->resource->id,
                "name" => $this->resource->name,
                "email" => $this->resource->email,
                "divisi" => $this->resource->divisi,
                "jenis_user" => $this->resource->jenis_user,
                "token" =>$this->whenNotNull($this->token)
            ],
            "success" => $this->msg
        ];
    }
}
