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

    public function guardarCliente(Request $request)  
    {
        $input = $request->all();
        $obj = (object)$input;

        //VERIFICAR CORREO ELECTRONICO DEL CLIENTE
        $ClaveEF_Cliente=Clientes::verificarClienteEmail($obj->correoElectronico);
        
        if($ClaveEF_Cliente) {
            $respuesta=$ClaveEF_Cliente;
        }
        else {   
            $respuesta=Clientes::guardarCliente(
                '3', //$obj->empresa,// *default
                $obj->equipo,// ip
                'mily',//$obj->usuario,// *default
                '3001',//$obj->claveInmueble,// *opcional
                $obj->razonsocial,
                $obj->rfc,
                $obj->correoElectronico,
                $obj->personaFisica,
                '100000205',//$obj->claveEF_Empresa, // *default
                $obj->calle,
                $obj->numeroExterior,
                $obj->numeroInterior,
                $obj->colonia,
                $obj->localidad,
                $obj->municipio,
                '15',//$obj->claveEstado, //*opcional
                '1',//$obj->clavePais,   //*opcional
                $obj->codigoPostal,
                '120',//$obj->claveTipoDeCliente,// *default
                $obj->esEspecial,            
                '100000210',//$obj->claveEntidadFiscalVendedor, // *default
                '8',//$obj->claveClasificador, // *default
                $obj->listaDeContactos, //[nombre,observaciones]
                $obj->listaDeTelefonos  //[codigoDePais,codigoDeZona,numero,extension,esCelular]
            );
        }
        return response()->json($respuesta, ApiStatus::OK);
    }
}
