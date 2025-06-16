<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectResourceCollection extends ResourceCollection
{
    public $msg;
    public function __construct($resource, $msg)
    {
        parent::__construct($resource);
        $this->msg =$msg;
    }
    public function toArray($request)
    {
        return [
            "data" => $this->collection,
            "success" => $this->msg
        ];
    }
}
