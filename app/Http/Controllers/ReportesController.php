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

    public function datosClienteMaxCtz(Request $request)
    {
        $input = $request->all();
        $datos = (object)$input;
        $respuesta = Reportes::ObtenerClienteMaxCtz($datos->claveEF_Inmueble, $datos->claveEF_Usuario, $datos->fechaInicio, $datos->fechaFinal, $datos->montoCotizado);
        return response()->json($respuesta, ApiStatus::OK);
    }

    public function datosClienteDscMax(Request $request)
    {
        $input = $request->all();
        $datos = (object)$input;
        $respuesta = Reportes::ObtenerClienteDscMax($datos->claveEF_Inmueble, $datos->claveEF_Usuario, $datos->fechaInicio, $datos->fechaFinal, $datos->montoDescuento);
        return response()->json($respuesta, ApiStatus::OK);
    }

    public function datosClienteMaxFac(Request $request)
    {
        $input = $request->all();
        $datos = (object)$input;
        $respuesta = Reportes::ObtenerClienteMaxFac($datos->claveEF_Inmueble, $datos->claveEF_Usuario, $datos->fechaInicio, $datos->fechaFinal, $datos->montoFacturado);
        return response()->json($respuesta, ApiStatus::OK);
    }

    public function datosClienteDscFac(Request $request)
    {
        $input = $request->all();
        $datos = (object)$input;
        $respuesta = Reportes::ObtenerClienteDscFac($datos->claveEF_Inmueble, $datos->claveEF_Usuario, $datos->fechaInicio, $datos->fechaFinal, $datos->desctoFacturado);
        return response()->json($respuesta, ApiStatus::OK);
    }
}
