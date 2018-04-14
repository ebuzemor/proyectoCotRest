<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MyClass\ApiStatus;
use App\Models\CondicionesComercialesCtz;
use App\Models\TipoEstatusCtz;
use App\Models\Cotizacion;
use App\Mail\Reminder;
use Mail;
use PDF;

class CotizacionController extends Controller
{
    function __construct()
    {
    	$this->middleware('api');
    }

    public function mostrarCondicionesComercialesCtz($claveEF_Empresa)
    {
    	$response = CondicionesComercialesCtz::where('claveEntidadFiscalEmpresa', $claveEF_Empresa)->first();
    	return response()->json($response, ApiStatus::OK);
    }

    public function obtenerTipoEstatusCtz()
    {
    	$response = TipoEstatusCtz::where('claveTipoDeComprobante', 16)->limit(3)->get();
        //$response = TipoEstatusCtz::where('claveTipoDeComprobante', 16)->get();
    	return response()->json($response, ApiStatus::OK);
    }

    public function editarCotizacion(Request $request)
    {
        $input = $request->all();
        $objeto = (object)$input;

        $respuesta = Cotizacion::editarCotizacion(
            $objeto->Empresa,
            $objeto->Equipo,
            $objeto->Usuario,
            $objeto->ClaveInmueble,
            $objeto->ClaveTipoDeComprobante,
            $objeto->FechaEmision,
            $objeto->Partidas,
            $objeto->ClaveMoneda, 
            $objeto->ClaveEntidadFiscalInmueble,
            $objeto->ClaveTipoEstatusRecepcion,
            $objeto->ClaveEntidadFiscalResponsable,
            $objeto->ClaveComprobante,
            $objeto->CodigoDeComprobante,// AGREGAR CAMPO
            $objeto->ListaComprobantesImpuestos,
            $objeto->ClaveEntidadFiscalCliente,
            $objeto->ClaveListaDePrecios,
            $objeto->FechaVigencia,
            $objeto->SubTotal,
            $objeto->Descuento,
            $objeto->Impuestos,
            $objeto->Total,
            $objeto->Observaciones,
            $objeto->DetallesDeComprobante
        );
        return response()->json($respuesta, ApiStatus::OK);
    }

    public function guardarCotizacion(Request $request)
    {        
        $input = $request->all();
        $objeto = (object)$input;

        $respuesta = Cotizacion::ejecutarGuardar(        	
            $objeto->Empresa,
            $objeto->Equipo,
            $objeto->Usuario,
            $objeto->ClaveInmueble,
            $objeto->ClaveTipoDeComprobante,
            $objeto->FechaEmision,
            $objeto->Partidas,
            $objeto->ClaveMoneda, 
            $objeto->ClaveEntidadFiscalInmueble,
            $objeto->ClaveTipoEstatusRecepcion,
            $objeto->ClaveEntidadFiscalResponsable,
            $objeto->ListaComprobantesImpuestos,
            $objeto->ClaveEntidadFiscalCliente,
            $objeto->ClaveListaDePrecios,
            $objeto->FechaVigencia,
            $objeto->SubTotal,
            $objeto->Descuento,
            $objeto->Impuestos,
            $objeto->Total,
            $objeto->Observaciones,
            $objeto->DetallesDeComprobante
       	);     
        return response()->json($respuesta, ApiStatus::OK);
    }
    
    public function mostrarCotizaciones(Request $request)
    {
        $datos = (object)$request->all();
        $respuesta = Cotizacion::mostrarCotizaciones($datos->claveEF_Inmueble, $datos->claveEF_Responsable, $datos->fechaInicial,
                                                     $datos->fechaFinal, $datos->txtCliente, $datos->claveTipoEstatus);
        return response()->json($respuesta, ApiStatus::OK);
    }

    public function cargarDetallesCotizacion($claveComprobante)
    {
        $respuesta = Cotizacion::cargarDetallesCotizacion($claveComprobante);
        return response()->json($respuesta, ApiStatus::OK);
    }

    public function enviarMail(Request $request)
    {        
        try {
            $pdf = Cotizacion::descargarPDF($request->claveEF_Empresa, $request->claveComprobante, $request->fichaTecnica);
            $correos = explode(",", $request->emails);
            Mail::to($correos)->send(new Reminder($pdf, $request->claveComprobante));
            $success = true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
        if($success) {
          return response()->json(["mensaje" => "E-mail Se Ha Enviado Correctamente"], ApiStatus::OK);
        }       
    }

    public function descargarPDF($claveEF_Empresa, $claveComprobante, $fichaTecnica)
    {
        $datos = Cotizacion::descargarPDF($claveEF_Empresa, $claveComprobante, $fichaTecnica);
        return $datos->download();
    }
}
