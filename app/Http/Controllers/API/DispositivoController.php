<?php

namespace App\Http\Controllers\API;

use App\Models\Comercio;
use App\Models\Usuario;
use App\Models\Dispositivo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DispositivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dispositivo = Dispositivo::all();
        return response($dispositivo);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   

        $validator = Validator::make($request->all(), [
            'rut_comercio' => 'required',//hace match con el id
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'rut_comercio is required'
            ], 422);
        }
        //verificamos que elcomercio existe en la basee de datos con el rut y extraemos el id
        $rut_c = str_replace([' ', '.'], '', $request->rut_comercio);

        $comercio = Comercio::where("rut", $rut_c)->first();
        if (!$comercio){
            return response()->json([
                'message' => 'comercio does not exist '
            ], 404);
        }

        $dispositivo = new Dispositivo;
        $dispositivo->comercio_id = $comercio->id;
        $dispositivo->save();
        return response($dispositivo);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dispositivo = Dispositivo::find($id);
        if (!$dispositivo){
            return response()->json([
                'message' => 'dispositivo not found'
            ], 404);
        }
        return response($dispositivo);
    }

    public function logInUsuario(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'dispositivo_id' => 'required',
            'rut_usuario' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'dispositivo_id and rut_usuario are required'
            ], 422);
        }

        $rut = str_replace([' ', '.'], '', $request->rut_usuario);
        if (!Usuario::validarRutUsuario($rut)){
            return response()->json([
                'message' => 'rut is not valid'
            ], 409);
        }

        $usuario = Usuario::where("rut",$rut)->first();
        if (!$usuario){
            return response()->json([
                'message' => 'usuario does not exist'
            ], 404);
        }
        $dispositivo = Dispositivo::find($request->dispositivo_id);
        if (!$dispositivo){
            return response()->json([
                'message' => 'dispositivo does not exist'
            ], 404);
        }
        $dispositivo->usuario_id = $usuario->id;
        $dispositivo->save();

        return response()->json([
            'id' => $dispositivo->id,
            'usuario_id' => $dispositivo->usuario_id,
            'message' => 'Usuario accedio en dispositivo exitosamente'
        ], 200);
    }

    public function logOutUsuario(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dispositivo_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'dispositivo_id is required'
            ], 422);
        }

        $dispositivo = Dispositivo::find($request->dispositivo_id);
        if (!$dispositivo){
            return response()->json([
                'message' => 'dispositivo does not exist'
            ], 404);
        }

        if ($dispositivo->usuario_id == 0){
            return response()->json([
                'message' => 'no users logged in this device'
            ], 409);
        }

        $dispositivo->usuario_id = 0;
        $dispositivo->save();

        return response()->json([
            'id' => $dispositivo->id,
            'usuario_id' => $dispositivo->usuario_id,
            'message' => 'Usuario salio del dispositivo exitosamente'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dispositivo $dispositivo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dispositivo $dispositivo)
    {
        //
    }
}
