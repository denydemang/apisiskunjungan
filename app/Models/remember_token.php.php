<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class remember_token extends Model
{
    protected $table ="remember_token";
    protected $primaryKey ="id";
    protected $keyType ="int";
}
