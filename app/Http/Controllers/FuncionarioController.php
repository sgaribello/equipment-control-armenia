<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para gestionar funcionarios de la Alcaldía de Armenia
 * 
 * @author Santiago Garibello <sgaribello@github.com>
 * @version 1.0.0
 */
class FuncionarioController extends Controller
{
    public function index()
    {
        $funcionarios = Funcionario::with('equipos')->get();
        return response()->json(['success' => true, 'data' => $funcionarios]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_completo' => 'required|string|max:150',
            'documento' => 'required|string|max:20|unique:armenia_funcionarios,documento',
            'cargo' => 'required|string|max:100',
            'dependencia' => 'required|string|max:150',
            'correo_institucional' => 'required|email|max:100|unique:armenia_funcionarios,correo_institucional',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $funcionario = Funcionario::create($request->all());
        return response()->json(['success' => true, 'message' => 'Funcionario creado exitosamente', 'data' => $funcionario], 201);
    }

    public function show($id)
    {
        $funcionario = Funcionario::with(['equipos.movimientos'])->find($id);
        if (!$funcionario) {
            return response()->json(['success' => false, 'message' => 'Funcionario no encontrado'], 404);
        }
        return response()->json(['success' => true, 'data' => $funcionario]);
    }

    public function update(Request $request, $id)
    {
        $funcionario = Funcionario::find($id);
        if (!$funcionario) {
            return response()->json(['success' => false, 'message' => 'Funcionario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_completo' => 'sometimes|required|string|max:150',
            'documento' => 'sometimes|required|string|max:20|unique:armenia_funcionarios,documento,' . $id,
            'cargo' => 'sometimes|required|string|max:100',
            'dependencia' => 'sometimes|required|string|max:150',
            'correo_institucional' => 'sometimes|required|email|max:100|unique:armenia_funcionarios,correo_institucional,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $funcionario->update($request->all());
        return response()->json(['success' => true, 'message' => 'Funcionario actualizado exitosamente', 'data' => $funcionario]);
    }

    public function destroy($id)
    {
        $funcionario = Funcionario::find($id);
        if (!$funcionario) {
            return response()->json(['success' => false, 'message' => 'Funcionario no encontrado'], 404);
        }

        if (!$funcionario->canBeDeleted()) {
            return response()->json(['success' => false, 'message' => 'No se puede eliminar el funcionario porque tiene equipos asignados'], 400);
        }

        $funcionario->delete();
        return response()->json(['success' => true, 'message' => 'Funcionario eliminado exitosamente']);
    }
}
