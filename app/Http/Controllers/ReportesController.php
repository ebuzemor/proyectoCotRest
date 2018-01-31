<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MyClass\ApiStatus;
use App\Models\Reportes;

class ReportesController extends Controller
{
    public function reporteCotizaciones(Request $request)
    {
    	$input = $request->all();
    	$datos = (object)$input;
    	$respuesta = Reportes::ReportesCotizacionesUsr($datos->claveEF_Inmueble, $datos->claveEF_Usuario, $datos->fechaInicio, $datos->fechaFinal);
    	return response()->json($respuesta, ApiStatus::OK);
    }

    public function listaCotizaciones(Request $request)
    {
    	$input = $request->all();
    	$datos = (object)$input;
    	$respuesta = Reportes::ListaCotizacionesUsr($datos->claveEF_Inmueble, $datos->claveEF_Usuario, $datos->fechaInicio, $datos->fechaFinal);
    	return response()->json($respuesta, ApiStatus::OK);
    }
}
