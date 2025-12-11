<?php

namespace App\Http\Controllers;

use App\Models\MovimientoEquipo;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para gestionar movimientos de equipos de la Alcaldía de Armenia
 * 
 * @author Santiago Garibello <sgaribello@github.com>
 * @version 1.0.0
 */
class MovimientoEquipoController extends Controller
{
    public function index(Request $request)
    {
        $query = MovimientoEquipo::with('equipo.funcionario');

        if ($request->has('tipo_movimiento')) {
            $query->tipoMovimiento($request->tipo_movimiento);
        }

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->entreFechas($request->fecha_inicio, $request->fecha_fin);
        }

        $movimientos = $query->orderBy('fecha_hora', 'desc')->get();
        return response()->json(['success' => true, 'data' => $movimientos]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'equipo_id' => 'required|exists:armenia_equipos,id',
            'tipo_movimiento' => 'required|in:asignación,traslado,devolución,baja,reparación',
            'fecha_hora' => 'required|date',
            'motivo' => 'required|string',
            'realizado_por' => 'required|string|max:150',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $movimiento = MovimientoEquipo::create($request->all());
        return response()->json(['success' => true, 'message' => 'Movimiento registrado exitosamente', 'data' => $movimiento->load('equipo')], 201);
    }

    public function show($id)
    {
        $movimiento = MovimientoEquipo::with('equipo.funcionario')->find($id);
        if (!$movimiento) {
            return response()->json(['success' => false, 'message' => 'Movimiento no encontrado'], 404);
        }
        return response()->json(['success' => true, 'data' => $movimiento]);
    }

    public function getByEquipo($equipoId)
    {
        $equipo = Equipo::find($equipoId);
        if (!$equipo) {
            return response()->json(['success' => false, 'message' => 'Equipo no encontrado'], 404);
        }

        $movimientos = MovimientoEquipo::where('equipo_id', $equipoId)
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $movimientos]);
    }

    public function getByFuncionario($funcionarioId)
    {
        $movimientos = MovimientoEquipo::whereHas('equipo', function($query) use ($funcionarioId) {
            $query->where('funcionario_id', $funcionarioId);
        })->with('equipo')->orderBy('fecha_hora', 'desc')->get();

        return response()->json(['success' => true, 'data' => $movimientos]);
    }
}
