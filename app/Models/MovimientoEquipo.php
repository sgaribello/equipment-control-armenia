<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoEquipo extends Model
{
    use HasFactory;

    protected $table = 'armenia_movimientos_equipos';

    protected $fillable = [
        'equipo_id',
        'tipo_movimiento',
        'fecha_hora',
        'motivo',
        'realizado_por',
        'observaciones',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function scopeTipoMovimiento($query, $tipo)
    {
        return $query->where('tipo_movimiento', $tipo);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
    }
}