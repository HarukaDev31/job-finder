<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postulacion extends Model
{
    use HasFactory;
    protected $table = 'postulaciones';
    protected $fillable = [
        'trabajo_id',
        'postulante_id',
        'mensaje',
        'cv_path',
        'estado',
    ];

    /**
     * Relación con el trabajo
     */
    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class);
    }

    /**
     * Relación con el postulante
     */
    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }
}
