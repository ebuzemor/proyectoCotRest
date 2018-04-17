<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class pruebaController extends Controller
{
    public function imagenes()
    {
    	$ClaveComprobante = 300000536;
    	$consulta = DB::select("SELECT * 
                                    FROM copicoods.detallesdecomprobantes d 
                                    JOIN copicoods.detallesdecomprobantes_diasdeentrega e 
                                    USING (claveDetalleDeComprobante)
                                    WHERE d.claveComprobante = ?", [$ClaveComprobante]);
    	$consulta = (array)$consulta;
    	return view('prueba', compact('consulta'));
    }
}
