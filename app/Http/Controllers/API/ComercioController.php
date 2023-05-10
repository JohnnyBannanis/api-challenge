<?php

namespace App\Http\Controllers\API;

use App\Models\Comercio;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComercioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comercios = Comercio::all();
        return response($comercios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   

        $validator = Validator::make($request->all(), [
            'rut' => 'required',
            'nombre' => 'required',
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'message' => 'rut and nombre are required'
            ], 422);
        }
        $rut = str_replace([' ', '.'], '', $request->rut);
        if (!Comercio::validarRutComercio($rut)){
            return response()->json([
                'message' => 'rut is not valid'
            ], 409);
        }

        $in_db = Comercio::where("rut", $rut)->first();
        if ($in_db){
            return response()->json([
                'message' => 'rut allready in use'
            ], 409);
        }

        $comercio = new Comercio;
        $comercio->rut = $rut; 
        $comercio->nombre = $request->nombre;
        $comercio->save();
        return response($comercio);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $comercio = Comercio::find($id);
        if (!$comercio){
            return response()->json([
                'message' => 'comercio not found'
            ], 404);
        }
        return response($comercio);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        $comercio = Comercio::find($id);
        if (!$comercio){
            return response()->json([
                'message' => 'comercio not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'message' => 'nombre is required'
            ], 422);
        }

        $comercio->nombre = $request->nombre;
        $comercio->save();
        return response()->json([
            "id"=> $comercio->id,
            "rut"=> $comercio->rut,
            "nombre"=> $comercio->nombre,
            "saldo_pts"=> $comercio->saldo_pts,
            "created_at"=> $comercio->created_at,
            "updated_at"=> $comercio->updated_at,
            'message' => 'comercio successfully updated'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
