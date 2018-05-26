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
							, nombreVendedor
							, claveInmueble AS sucursal
							, SUM(numcotizaciones) AS num_cotizaciones
							, SUM(CASE WHEN claveTipoDeStatusDeComprobante = 160 THEN numcotizaciones ELSE 0 END) AS borrador
							, SUM(CASE WHEN claveTipoDeStatusDeComprobante = 161 THEN numcotizaciones ELSE 0 END) AS pendientes
							, SUM(CASE WHEN claveTipoDeStatusDeComprobante = 162 THEN numcotizaciones ELSE 0 END) AS autorizadas
							, SUM(CASE WHEN claveTipoDeStatusDeComprobante = 163 THEN numcotizaciones ELSE 0 END) AS canceladas
							, SUM(CASE WHEN claveTipoDeStatusDeComprobante = 164 THEN numcotizaciones ELSE 0 END) AS facturadas
							, MAX(numcotizaciones) AS total_ctzs_cte	
							, SUM(montocotizaciones) AS total_ctzs	
							, MAX(montocotizaciones) AS max_ctz_cte	 	
							, SUM(montodescuentos) AS total_desctos
							, MAX(montodescuentos) AS max_descto_cte
							, SUM(CASE WHEN claveTipoDeStatusDeComprobante = 164 THEN montocotizaciones ELSE 0 END) AS total_ctzs_fact
							, MAX(CASE WHEN claveTipoDeStatusDeComprobante = 164 THEN montocotizaciones ELSE 0 END) max_ctz_cte_fact
							, SUM(CASE WHEN claveTipoDeStatusDeComprobante = 164 THEN montodescuentos ELSE 0 END) AS total_ctzs_descto_fact
							, MAX(CASE WHEN claveTipoDeStatusDeComprobante = 164 THEN montodescuentos ELSE 0 END) AS max_descto_cte_fact
						FROM
						(
							SELECT 
								u.claveEntidadFiscalUsuario
								, u.nickname
								, f.razonSocial AS nombreVendedor
								, z.claveEntidadFiscalCliente
								, e.razonSocial AS nombreCliente
								, c.claveEntidadFiscalInmueble
								, i.claveInmueble
								, c.claveTipoDeStatusDeComprobante
								, COUNT(*) AS numcotizaciones
								, ROUND(SUM(z.total), 2) AS montocotizaciones
								, ROUND(SUM(z.descuento), 2) AS montodescuentos
							FROM comprobantes c
							JOIN cotizaciones z ON c.claveComprobante = z.claveComprobanteDeCotizacion
							JOIN usuarios u ON c.claveEntidadFiscalResponsable = u.claveEntidadFiscalUsuario
							JOIN entidadesfiscales e ON z.claveEntidadFiscalCliente = e.claveEntidadFiscal
							JOIN entidadesfiscales f ON c.claveEntidadFiscalResponsable = f.claveEntidadFiscal
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
