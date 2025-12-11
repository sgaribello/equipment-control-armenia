<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para gestionar equipos de la Alcaldía de Armenia
 * 
 * @author Santiago Garibello <sgaribello@github.com>
 * @version 1.0.0
 */
class EquipoController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipo::with(['funcionario', 'ultimoMovimiento']);

        if ($request->has('funcionario_id')) {
            $query->where('funcionario_id', $request->funcionario_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        $equipos = $query->get();
        return response()->json(['success' => true, 'data' => $equipos]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'funcionario_id' => 'required|exists:armenia_funcionarios,id',
            'tipo_equipo' => 'required|string|max:50',
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'serial' => 'required|string|max:50|unique:armenia_equipos,serial',
            'placa_inventario' => 'nullable|string|max:50',
            'estado' => 'required|in:activo,dañado,en reparación,baja',
            'fecha_asignacion' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $equipo = Equipo::create($request->all());
        return response()->json(['success' => true, 'message' => 'Equipo creado exitosamente', 'data' => $equipo->load('funcionario')], 201);
    }

    public function show($id)
    {
        $equipo = Equipo::with(['funcionario', 'movimientos'])->find($id);
        if (!$equipo) {
            return response()->json(['success' => false, 'message' => 'Equipo no encontrado'], 404);
        }
        return response()->json(['success' => true, 'data' => $equipo]);
    }

    public function update(Request $request, $id)
    {
        $equipo = Equipo::find($id);
        if (!$equipo) {
            return response()->json(['success' => false, 'message' => 'Equipo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'funcionario_id' => 'sometimes|required|exists:armenia_funcionarios,id',
            'tipo_equipo' => 'sometimes|required|string|max:50',
            'marca' => 'sometimes|required|string|max:50',
            'modelo' => 'sometimes|required|string|max:50',
            'serial' => 'sometimes|required|string|max:50|unique:armenia_equipos,serial,' . $id,
            'placa_inventario' => 'nullable|string|max:50',
            'estado' => 'sometimes|required|in:activo,dañado,en reparación,baja',
            'fecha_asignacion' => 'sometimes|required|date',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $equipo->update($request->all());
        return response()->json(['success' => true, 'message' => 'Equipo actualizado exitosamente', 'data' => $equipo->load('funcionario')]);
    }

    public function updateStatus(Request $request, $id)
    {
        $equipo = Equipo::find($id);
        if (!$equipo) {
            return response()->json(['success' => false, 'message' => 'Equipo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:activo,dañado,en reparación,baja',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $equipo->update(['estado' => $request->estado]);
        return response()->json(['success' => true, 'message' => 'Estado del equipo actualizado exitosamente', 'data' => $equipo]);
    }

    public function destroy($id)
    {
        $equipo = Equipo::find($id);
        if (!$equipo) {
            return response()->json(['success' => false, 'message' => 'Equipo no encontrado'], 404);
        }

        if (!$equipo->canBeDeleted()) {
            return response()->json(['success' => false, 'message' => 'No se puede eliminar el equipo porque tiene movimientos históricos registrados'], 400);
        }

        $equipo->delete();
        return response()->json(['success' => true, 'message' => 'Equipo eliminado exitosamente']);
    }
}
