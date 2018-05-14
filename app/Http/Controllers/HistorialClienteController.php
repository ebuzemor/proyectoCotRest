<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MyClass\ApiStatus;
use App\Models\HistorialCliente;

class HistorialClienteController extends Controller
{
    public function ListaCtzGeneradas($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente)
    {
    	$response = HistorialCliente::obtenerCtzGeneradas($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente);
    	return response()->json($response, ApiStatus::OK);
    }

    public function ListaCtzFacturadas($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente)
    {
    	$response = HistorialCliente::obtenerCtzFacturadas($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente);
    	return response()->json($response, ApiStatus::OK);
    }

    public function ListaProductosCotizados($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente)
    {
    	$response = HistorialCliente::obtenerProductosCotizados($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente);
    	return response()->json($response, ApiStatus::OK);
    }

    public function ListaProductosVendidos($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente)
    {
    	$response = HistorialCliente::obtenerProductosVendidos($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente);
    	return response()->json($response, ApiStatus::OK);
    }
}