<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'armenia_equipos';

    protected $fillable = [
        'funcionario_id',
        'tipo_equipo',
        'marca',
        'modelo',
        'serial',
        'placa_inventario',
        'estado',
        'fecha_asignacion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoEquipo::class, 'equipo_id')->orderBy('fecha_hora', 'desc');
    }

    public function canBeDeleted(): bool
    {
        return $this->movimientos()->count() === 0;
    }

    public function ultimoMovimiento()
    {
        return $this->hasOne(MovimientoEquipo::class, 'equipo_id')->latestOfMany('fecha_hora');
    }
}