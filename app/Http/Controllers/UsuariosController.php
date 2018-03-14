<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios;
use App\MyClass\ApiStatus;
use Auth;
use Validator;

class UsuariosController extends Controller
{    
    public function login()
    {
    	if(Auth::attempt(['name' => request('name'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('Cotizador')->accessToken;
            return response()->json($success, ApiStatus::OK);
        } else {
            return response()->json(['error' => 'Acceso No Autorizado'], ApiStatus::UNAUTHORIZED);
        }
    }

    public function buscar($claveEF_Empresa, $usuario, $password)
    {
        $buscarUsuarios = Usuarios::buscarUsuario($claveEF_Empresa, $usuario, $password);
        if($buscarUsuarios != null)
            return response()->json(['usuarios' => $buscarUsuarios], ApiStatus::OK);
        else
            return response()->json(['usuarios' => 'Sin datos'], ApiStatus::NO_CONTENT);
    }

    public function permisos($claveEF_Empresa, $claveEF_Usuario)
    {
        $permisosUsuarios = Usuarios::obtenerPermisos($claveEF_Empresa, $claveEF_Usuario);
        if($permisosUsuarios != null)
            return response()->json(['permisos' => $permisosUsuarios], ApiStatus::OK);
        else
            return response()->json(['permisos' => 'Sin datos'], ApiStatus::NO_CONTENT);
    }

    public function cargarPermisos($claveAplicacion)
    {
        $permisos = Usuarios::cargarPermisos($claveAplicacion);
        if($permisos != null)
            return response()->json(['acciones' => $permisos], ApiStatus::OK);
        else
            return response()->json(['acciones' => 'Sin datos'], ApiStatus::NO_CONTENT);
    }

    /*public function obtenerProcesos($claveEF_Usuario)
    {
        $procesosAutorizados = Usuarios::obtenerProcesosAutorizados($claveEF_Usuario);
        if($procesosAutorizados != null)
            return response()->json(['procesosAutorizados' => $procesosAutorizados], ApiStatus::OK);
        else 
            return response()->json(['procesosAutorizados' => 'Sin datos'], ApiStatus::NO_CONTENT);
    }

    public function cargarProcesos($claveAplicacion)
    {
        $cargarProcesos = Usuarios::cargarProcesosAutorizados($claveAplicacion);
        if($cargarProcesos != null)
            return response()->json(['cargarProcesos' => $cargarProcesos], ApiStatus::OK);
        else
            return response()->json(['cargarProcesos' => 'Sin datos'], ApiStatus::NO_CONTENT);
    }*/
}
