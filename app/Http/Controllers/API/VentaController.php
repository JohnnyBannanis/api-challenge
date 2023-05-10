<?php

namespace App\Http\Controllers\API;

use App\Models\Comercio;
use App\Models\Dispositivo;
use App\Models\Venta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ventas = Venta::all();
        return response($ventas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'monto' => 'required',
            'dispositivo_id' => 'required',
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'message' => 'monto and dispositivo_id is required'
            ], 422);
        }
        //verificamos que el dispositivo existe en la basee de datos mediante su id con elmetodo find para encontrar el registro exavto
        $dispositivo = Dispositivo::find($request->dispositivo_id);
        if (!$dispositivo){
            return response()->json([
                'message' => 'dispositivo does not exist'
            ], 404);
        }
        //se verifica que el comercio existe para un dispositivo y se utiliza este para sumar saldo_pts
        $comercio = Comercio::find($dispositivo->comercio_id);
        if (!$dispositivo){
            return response()->json([
                'message' => 'comercio does not exist'
            ], 404);
        }

        $venta = new Venta;
        $venta->monto = $request->monto; 
        $venta->dispositivo_id = $dispositivo->id;
        $venta->usuario_id = $dispositivo->usuario_id;
        $venta->codigo_seg = Crypt::encryptString((Str::random(8)));
        $venta->anulada = false;
        $venta->save();
        
        $comercio->saldo_pts = $comercio->saldo_pts + 10;
        $comercio->save();

        return response($venta);
    }

    public function anular(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'codigo_seg' => 'required',//hace match con el id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'id and codigo_seg are required'
            ], 422);
        }

        $venta = Venta::find($request->id);

        if (!$venta){
            return response()->json([
                'message' => 'venta does not exist'
            ], 404);
        }

        if (!($request->codigo_seg == Crypt::decryptString($venta->codigo_seg))){
            return response()->json([
                'message' => 'codigo_seg and id missmatch, this venta does not exist'
            ], 404);
        }

        if ($venta->anulada){
            return response()->json([
                'message' => 'ERROR: La venta ya esta anulada'
            ], 409);
        }

        $dispositivo = Dispositivo::find($venta->dispositivo_id);

        if (!$dispositivo){
            return response()->json([
                'message' => 'dispositivo does not exist'
            ], 404);
        }
        
        //se verifica que el comercio existe para un dispositivo y se utiliza este para sumar saldo_pts
        $comercio = Comercio::find($dispositivo->comercio_id);

        if (!$dispositivo){
            return response()->json([
                'message' => 'comercio does not exist'
            ], 404);
        }

        $venta->anulada = true;
        $venta->save();

        $comercio->saldo_pts = $comercio->saldo_pts - 10;
        $comercio->save();

        return response()->json([
            'id' => $venta->id,
            'anulada' => $venta->anulada,
            'codigo_seg' => Crypt::decryptString($venta->codigo_seg),
            'dispositivo_id' => $dispositivo->id,
            'comercio_id' => $comercio->id,
            'saldo_pts' => $comercio->saldo_pts,
            'message' => 'Venta anulada exitosamente'
        ], 200);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $venta = Venta::find($id);
        if (!$venta){
            return response()->json([
                'message' => 'venta not found'
            ], 404);
        }
        return response()->json([
            "id" => $venta->id,
            "monto" => $venta->monto,
            "anulada" => $venta->anulada,
            "codigo_seg" => Crypt::decryptString($venta->codigo_seg),
            "created_at" => $venta->created_at,
            "updated_at" => $venta->updated_at,
            "dispositivo_id" => $venta->dispositivo_id,
            "usuario_id" => $venta->usuario_id
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        //
    }
}
