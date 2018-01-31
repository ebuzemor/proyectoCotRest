<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Reportes extends Model
{
    public static function ReportesCotizacionesUsr($claveEF_Inmueble, $claveEF_Usuario, $fechaInicio, $fechaFinal)
    {
    	$filtroInmueble = "";
    	$filtroUsuario = "";
    	$filtroFechas = "";

    	if($claveEF_Inmueble != null)
    		$filtroInmueble = " AND c.claveEntidadFiscalInmueble = $claveEF_Inmueble";

    	if($claveEF_Usuario != null)
    		$filtroUsuario = " AND c.claveEntidadFiscalResponsable = $claveEF_Usuario";

    	if($fechaInicio != null && $fechaFinal != null)
    		$filtroFechas = " AND c.fechaEmision BETWEEN '$fechaInicio' AND '$fechaFinal'";

    	$consulta = DB::connection('copico')
    				->select("
    					SELECT
							claveEntidadFiscalUsuario
							, nickname
							, claveInmueble AS sucursal
							, SUM(numcotizaciones) AS num_cotizaciones
							, SUM(CASE WHEN claveTipoDeStatusDeComprobante = 160 THEN numcotizaciones ELSE 0 END) AS borrador
							, SUM(CASE WHEN claveTipoDeStatusDeComprobante = 162 THEN numcotizaciones ELSE 0 END) AS definitiva	
							, SUM(montocotizaciones) AS total_cotizaciones
							, MAX(montocotizaciones) AS maxima_cotizacion_cliente	
							, MAX(numcotizaciones) AS total_cotizaciones_cliente
							, SUM(montodescuentos) AS total_descuentos
							, SUM(CASE WHEN montodescuentos > 0 THEN montodescuentos ELSE 0 END) AS total_cotizaciones_descuento
							, MAX(montodescuentos) AS maximo_descuento_cliente
						FROM
							(
						SELECT 
								u.claveEntidadFiscalUsuario
								, u.nickname
								, z.claveEntidadFiscalCliente
								, e.razonSocial
								, c.claveEntidadFiscalInmueble
								, i.claveInmueble
								, c.claveTipoDeStatusDeComprobante
								, COUNT(*) AS numcotizaciones
								, ROUND(SUM(z.total),2) AS montocotizaciones
								, ROUND(SUM(z.descuento),2) AS montodescuentos
						FROM comprobantes c
						JOIN cotizaciones z ON c.claveComprobante = z.claveComprobanteDeCotizacion
						JOIN usuarios u ON c.claveEntidadFiscalResponsable = u.claveEntidadFiscalUsuario
						JOIN entidadesfiscales e ON z.claveEntidadFiscalCliente = e.claveEntidadFiscal
						JOIN inmuebles i ON c.claveEntidadFiscalInmueble = i.claveEntidadFiscalInmueble
						WHERE 1 = 1
						$filtroInmueble
						$filtroUsuario
						$filtroFechas
						GROUP BY u.nickname, c.claveTipoDeStatusDeComprobante, z.claveEntidadFiscalCliente, e.razonSocial, i.claveInmueble
							) AS x
						GROUP BY claveEntidadFiscalUsuario, claveInmueble
    					");

    	return $consulta;
    }

	public static function ListaCotizacionesUsr($claveEF_Inmueble, $claveEF_Usuario, $fechaInicio, $fechaFinal)
	{
		$filtroInmueble = "";
		$filtroUsuario = "";
    	$filtroFechas = "";

    	if($claveEF_Inmueble != null)
    		$filtroInmueble = " AND c.claveEntidadFiscalInmueble = $claveEF_Inmueble";

    	if($claveEF_Usuario != null)
    		$filtroUsuario = " AND c.claveEntidadFiscalResponsable = $claveEF_Usuario";

    	if($fechaInicio != null && $fechaFinal != null)
    		$filtroFechas = " AND c.fechaEmision BETWEEN '$fechaInicio' AND '$fechaFinal'";

    	$consulta = DB::connection('copico')
    				->select("
    					SELECT 
							u.claveEntidadFiscalUsuario
							, u.nickname
							, z.claveEntidadFiscalCliente
							, e.razonSocial
							, c.claveEntidadFiscalInmueble
							, i.claveInmueble
							, c.claveTipoDeStatusDeComprobante
							, COUNT(*) AS numcotizaciones
							, ROUND(SUM(z.total),2) AS montocotizaciones
						FROM comprobantes c
						JOIN cotizaciones z ON c.claveComprobante = z.claveComprobanteDeCotizacion
						JOIN usuarios u ON c.claveEntidadFiscalResponsable = u.claveEntidadFiscalUsuario
						JOIN entidadesfiscales e ON z.claveEntidadFiscalCliente = e.claveEntidadFiscal
						JOIN inmuebles i ON c.claveEntidadFiscalInmueble = i.claveEntidadFiscalInmueble
						WHERE 1 = 1
						$filtroInmueble
						$filtroUsuario
						$filtroFechas
						GROUP BY u.nickname, c.claveTipoDeStatusDeComprobante, z.claveEntidadFiscalCliente, e.razonSocial, i.claveInmueble
    					");

    	return $consulta;
	}
}
