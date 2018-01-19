<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inmuebles;
use App\MyClass\ApiStatus;

class InmueblesController extends Controller
{
    function __construct()
    {
    	$this->middleware('api');
    }

    public function listaSucursales($claveEF_Empresa)
    {
    	$sucursales = Inmuebles::where('claveEntidadFiscalEmpresa', $claveEF_Empresa)->get();
    	return response()->json(['sucursales'=>$sucursales], ApiStatus::OK);
    }

    public function obtenerSucursal($claveEF_Inmueble)
    {
    	$sucursal = Inmuebles::where('claveEntidadFiscalInmueble', $claveEF_Inmueble)->get();
    	if($sucursal != null)
    		return response()->json(['sucursales'=>$sucursal], ApiStatus::OK);
    	else
    		return response()->json(['mensaje'=>'No existe'], ApiStatus::NOT_FOUND);
    }
}
