<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajo extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'sueldo',
        'activo',
    ];

    protected $casts = [
        'sueldo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Relación con las postulaciones
     */
    public function postulaciones()
    {
        return $this->hasMany(Postulacion::class);
    }

    /**
     * Obtener el número de postulantes
     */
    public function getPostulantesCountAttribute()
    {
        return $this->postulaciones()->count();
    }
}
