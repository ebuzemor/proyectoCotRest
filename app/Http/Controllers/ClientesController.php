<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
use App\MyClass\ApiStatus;
use Auth;

class ClientesController extends Controller
{
    function __construct()
    {
    	$this->middleware('api');
    }

    public function buscar($ClaveEF_Empresa, $txtBusqueda)
    {
    	$buscarClientes = Clientes::buscarClientes($ClaveEF_Empresa, $txtBusqueda);
    	return response()->json(['clientes'=>$buscarClientes], ApiStatus::OK);
    }
}
