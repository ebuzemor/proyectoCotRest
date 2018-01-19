<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Usuarios extends Model
{
	protected $connection = 'copico';

    protected $table = 'usuarios';

    public static function buscarUsuario($claveEF_Empresa, $usuario, $password)
    {
    	// APROSI - 100000205
    	$consulta = DB::connection('copico')->
    					select("SELECT 
								us.claveEntidadFiscalUsuario AS claveEntidadFiscalEmpleado
								, us.nickname AS nombreUsuario 
								, em.numeroDeEmpresa AS empresa
								, em.claveEntidadFiscalEmpresa
								, ef.razonSocial
								, im.claveInmueble AS sucursal
								, im.nombreCorto
								, im.claveEntidadFiscalInmueble
								FROM usuarios us
								LEFT JOIN usuarios_empresas ue USING(claveEntidadFiscalUsuario)
								LEFT JOIN empresas em USING(claveEntidadFiscalEmpresa)
								LEFT JOIN entidadesFiscales ef ON em.claveEntidadFiscalEmpresa = ef.claveEntidadFiscal
								LEFT JOIN inmuebles im USING(claveEntidadFiscalEmpresa)
								WHERE 
									em.claveEntidadFiscalEmpresa = $claveEF_Empresa									
									AND us.nickname = '$usuario' 
									AND us.password = '$password'
								");
    	return $consulta;
    }
}