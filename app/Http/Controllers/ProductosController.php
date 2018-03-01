<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Productos;
use App\MyClass\ApiStatus;
use Excel;

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

    public function sincronizarCatalogo(Request $request)
    {
        /*$catalogo = Productos::sincronizarCatalogo($request->claveEF_Inmueble)->toArray();
        //return response()->json($catalogo, ApiStatus::OK);
        /*$datos[] = ['sku', 'qty', 'price', 'group_price:Lista 2', 'group_price:Lista 3', 'group_price:Lista 4', 
                    'group_price:Lista 5', 'group_price:Lista 6', 'group_price:Lista 7', 'use_config_backorders', 'status'];
        foreach ($catalogo as $cat) {
            $datos[] += ;
        }
        return $datos;*/
        $datos = Productos::all()->toArray();
        return Excel::create('prueba', function($excel) use($datos)
        {
            $excel->sheet('Catalogo', function($sheet) use($datos)
            {
                $sheet->fromModel($datos);
            });
        })->download('csv');
    }
}
