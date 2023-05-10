<?php

namespace App\Http\Controllers\API;

use App\Models\Usuario;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = Usuario::all();
        return response($usuarios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   

        $validator = Validator::make($request->all(), [
            'rut' => 'required',
            'nombre' => 'required',
            'apellido' => 'required'
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'message' => 'rut|nombre|apellido are required'
            ], 422);
        }
        $rut = str_replace([' ', '.'], '', $request->rut);
        if (!Usuario::validarRutUsuario($rut)){
            return response()->json([
                'message' => 'rut is not valid'
            ], 409);
        }

        $in_db = Usuario::where("rut", $rut)->first();
        if ($in_db){
            return response()->json([
                'message' => 'rut allready in use'
            ], 409);
        }

        $usuario = new Usuario;
        $usuario->rut = $rut; 
        $usuario->nombre = $request->nombre;
        $usuario->apellido = $request->apellido;
        $usuario->save();
        return response($usuario);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario){
            return response()->json([
                'message' => 'usuario not found'
            ], 404);
        }
        return response($usuario);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario){
            return response()->json([
                'message' => 'usuario not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'apellido' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'nombre and apellido are required'
            ], 422);
        }
        
        $usuario->nombre = $request->nombre;
        $usuario->apellido = $request->apellido;

        $usuario->save();
        return response()->json([
            "id"=> $usuario->id,
            "rut"=> $usuario->rut,
            "nombre"=> $usuario->nombre,
            "apellido"=> $usuario->apellido,
            "created_at"=> $usuario->created_at,
            "updated_at"=> $usuario->updated_at,
            'message' => 'usuario successfully updated'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $usuario)
    {
        //
    }
}
