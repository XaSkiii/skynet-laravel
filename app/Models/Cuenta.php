<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    use HasFactory;

  
    protected $fillable = [
        'cliente_id',
        'numero_cuenta',
        'tipo_cuenta',
        'saldo_actual',
    ];

  
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}