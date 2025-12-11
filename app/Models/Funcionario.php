<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcionario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'armenia_funcionarios';

    protected $fillable = [
        'nombre_completo',
        'documento',
        'cargo',
        'dependencia',
        'correo_institucional',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'funcionario_id');
    }

    public function movimientos()
    {
        return $this->hasManyThrough(
            MovimientoEquipo::class,
            Equipo::class,
            'funcionario_id',
            'equipo_id'
        );
    }

    public function canBeDeleted(): bool
    {
        return $this->equipos()->count() === 0;
    }
}