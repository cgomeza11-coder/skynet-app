<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['nombre_completo', 'correo_electronico', 'telefono', 'direccion'];

    public function cuentas()
    {
        return $this->hasMany(Cuenta::class);
    }
}
