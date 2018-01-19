<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Productos;
use App\MyClass\ApiStatus;

class ProductosController extends Controller
{
    function __construct()
    {
    	$this->middleware('api');
    }

    public function buscarProductos($claveEF_Inmueble, $claveEF_Cliente, $txtBusqueda)
    {
    	$buscarProductos = Productos::buscarProductos($claveEF_Inmueble, $claveEF_Cliente, $txtBusqueda);
    	return response()->json(['productos' => $buscarProductos], ApiStatus::OK);
    }

    public function buscarExistencias($claveEF_Empresa, $claveProducto)
    {
    	$buscarExistencias = Productos::buscarExistencias($claveEF_Empresa, $claveProducto);
    	return response()->json(['existencias' => $buscarExistencias], ApiStatus::OK);
    }
}
