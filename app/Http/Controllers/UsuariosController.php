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
}
