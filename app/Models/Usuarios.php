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
								INNER JOIN inmueblesautorizados au ON im.claveEntidadFiscalInmueble = au.claveEntidadFiscalInmueble 
									AND us.claveEntidadFiscalUsuario = au.claveEntidadFiscalUsuario 
									AND em.claveEntidadFiscalEmpresa = au.claveEntidadFiscalEmpresa
								WHERE 
									em.claveEntidadFiscalEmpresa = $claveEF_Empresa									
									AND us.nickname = '$usuario' 
									AND us.password = '$password'
								GROUP BY im.claveEntidadFiscalInmueble
								ORDER BY im.claveInmueble
								");
    	return $consulta;
    }

    public static function obtenerPermisos($claveEF_Empresa, $claveEF_Usuario)
    {
    	$consulta = DB::connection('copico')->
    					select("
    						SELECT 
								c.claveSeccion
								, c.claveSubmodulo
								, c.descripcion 
								, c.constante
							FROM submodulos s
							JOIN modulos m ON s.claveModulo = m.claveModulo
							JOIN seccionesdesubmodulos c ON s.claveSubmodulo = c.claveSubmodulo
							JOIN permisos_seccionesdesubmodulos p ON c.claveSeccion = p.claveSeccion
							WHERE s.claveAplicacion = 100000005  
								AND p.claveEntidadFiscalEmpresa = $claveEF_Empresa 
								AND p.claveEntidadFiscalUsuario = $claveEF_Usuario
    						");
    	return $consulta;
    }

    public static function cargarPermisos($claveAplicacion)
    {
    	$consulta = DB::connection('copico')->
    					select("
    						SELECT 
								s.claveSeccion
								, b.claveSubmodulo
								, s.descripcion
								, s.constante
							FROM seccionesdesubmodulos s
							JOIN submodulos b ON s.claveSubmodulo=b.claveSubmodulo
							WHERE b.claveAplicacion = $claveAplicacion
    						");
    	return $consulta;
    }

    /*public static function obtenerProcesosAutorizados($claveEF_Usuario)
    {
    	$consulta = DB::connection('copico')->
    					select("
    						SELECT u.claveProceso, u.claveSubmodulo, p.proceso, p.descripcionProceso
							FROM usuariosprocesosautorizacion u
							JOIN procesosconautorizacion p ON u.claveProceso=p.claveProceso
							JOIN submodulos s ON u.claveSubmodulo=s.claveSubmodulo
							WHERE s.claveAplicacion = 100000005 AND u.claveEntidadFiscalUsuario = $claveEF_Usuario
    						");
    	return $consulta;
    }

    public static function cargarProcesosAutorizados($claveAplicacion)
    {
    	$consulta = DB::connection('copico')->
    					select("
    						SELECT p.claveProceso, p.proceso, p.descripcionProceso
							FROM procesosconautorizacion p
							JOIN submodulos s ON p.claveSubmodulo=s.claveSubmodulo
							WHERE s.claveAplicacion = $claveAplicacion
    						");
    	return $consulta;
    }*/
}