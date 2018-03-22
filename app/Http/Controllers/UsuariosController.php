<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios;
use App\MyClass\ApiStatus;
use App\Models\PermisosSeccionesdeSubmodulos;
use Auth;
use Validator;
use DB;

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

    public function obtenerUsuarios($txtBusqueda)
    {
        $usuarios = Usuarios::obtenerListaUsuarios($txtBusqueda);
        if($usuarios != null)
            return response()->json(['datosUsuarios' => $usuarios], ApiStatus::OK);
        else
            return response()->json(['datosUsuarios' => 'Sin datos'], ApiStatus::NO_CONTENT);
    }

    public function guardarPermisos(Request $request)
    {
        $input = $request->all();
        $usrpermisos = (object)$input;
        $borrados = PermisosSeccionesdeSubmodulos::where('claveSeccion', '>=', 27)
                    ->where('claveEntidadFiscalEmpresa', $usrpermisos->ClaveEFEmpresa)
                    ->where('claveEntidadFiscalUsuario', $usrpermisos->ClaveEFUsuario)->delete();
        //Se decodifican la lista de permisos para poder guardarlos
        $lista = json_decode($usrpermisos->ListaPermisos, true);
        $guardados = 0;
        foreach ($lista as $key => $fila) {
            if($fila['Activo'] == true) {
                $permisos = new PermisosSeccionesdeSubmodulos();
                $permisos->claveEntidadFiscalEmpresa = $usrpermisos->ClaveEFEmpresa;
                $permisos->claveEntidadFiscalUsuario = $usrpermisos->ClaveEFUsuario;
                $permisos->claveSeccion = $fila['claveSeccion'];
                $banSave = $permisos->save();
                $guardados += $banSave ? 1 : 0;
            }
        }
        if($guardados > 0)
            return response()->json(['EstatusPermisos' => 'OK'], ApiStatus::OK);
        else
            return response()->json(['EstatusPermisos' => 'NO'], ApiStatus::NO_CONTENT);
    }
}