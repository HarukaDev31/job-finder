<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'numero_documento',
        'tipo_documento',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con las postulaciones
     */
    public function postulaciones()
    {
        return $this->hasMany(Postulacion::class);
    }

    /**
     * Obtener el nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }
}
