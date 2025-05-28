<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SisKunjungan extends Model
{
    use HasFactory;
    protected $table = "sis_kunjungans";
    protected $primaryKey  ="id";
    protected $keyType ="int";
    public $incrementing = true;
    protected $guarded = [];
}
