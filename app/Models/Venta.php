<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    protected $fillable = [
        'monto',
        'anulada'
    ];
    protected $hidden = [
        'codigo_seg'
    ];
}
