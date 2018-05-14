<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class HistorialCliente extends Model
{
    public static function obtenerCtzGeneradas($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente)
    {
    	$consulta = DB::connection('copico')->
    				select(
	    				"SELECT 
						    co.claveComprobanteDeCotizacion
						    , cm.codigoDeComprobante
						    , co.claveEntidadFiscalCliente
						    , cm.claveEntidadFiscalInmueble 
						    , cm.claveEntidadFiscalResponsable
						    , date(co.fechaVigencia) as fechaVigencia
						    , date(cm.fechaEmision) as fechaEmision 
						    , ct.codigoDeCliente
						    , ef.rfc
						    , ef.razonSocial
						    , ef.correoElectronico
						    , cm.partidas
						    , tc.descripcion AS estatus 
						    , tc.claveTipoDeStatusDeComprobante AS claveEstatus
						    , co.subtotal
						    , co.descuento
						    , co.impuesto
						    , co.total
						    , co.observaciones
						 FROM cotizaciones co
						 JOIN comprobantes cm ON co.claveComprobanteDeCotizacion = cm.claveComprobante
						 JOIN clientes ct USING (claveEntidadFiscalCliente)
						 JOIN entidadesfiscales ef ON co.claveEntidadFiscalCliente = ef.claveEntidadFiscal
						 JOIN tiposdestatusdecomprobantes tc USING (claveTipoDeStatusDeComprobante)
						 WHERE tc.claveTipoDeStatusDeComprobante IN (160, 161, 162) 
						 AND cm.claveEntidadFiscalInmueble = $claveEF_Inmueble
						 AND DATE(cm.fechaEmision) BETWEEN '$fechaInicio' AND '$fechaFinal'
						 AND co.claveEntidadFiscalCliente = $claveEF_Cliente");
    	return $consulta;
    }

    public static function obtenerCtzFacturadas($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente)
    {
    	$consulta = DB::connection('copico')->
    				select(
	    				"SELECT 
						    co.claveComprobanteDeCotizacion
						    , cm.codigoDeComprobante
						    , co.claveEntidadFiscalCliente
						    , cm.claveEntidadFiscalInmueble 
						    , cm.claveEntidadFiscalResponsable
						    , date(co.fechaVigencia) as fechaVigencia
						    , date(cm.fechaEmision) as fechaEmision 
						    , ct.codigoDeCliente
						    , ef.rfc
						    , ef.razonSocial
						    , ef.correoElectronico
						    , cm.partidas
						    , tc.descripcion AS estatus 
						    , tc.claveTipoDeStatusDeComprobante AS claveEstatus
						    , co.subtotal
						    , co.descuento
						    , co.impuesto
						    , co.total
						    , co.observaciones
						 FROM cotizaciones co
						 JOIN comprobantes cm ON co.claveComprobanteDeCotizacion = cm.claveComprobante
						 JOIN clientes ct USING (claveEntidadFiscalCliente)
						 JOIN entidadesfiscales ef ON co.claveEntidadFiscalCliente = ef.claveEntidadFiscal
						 JOIN tiposdestatusdecomprobantes tc USING (claveTipoDeStatusDeComprobante)
						 WHERE tc.claveTipoDeStatusDeComprobante = 164
						 AND cm.claveEntidadFiscalInmueble = $claveEF_Inmueble
						 AND DATE(cm.fechaEmision) BETWEEN '$fechaInicio' AND '$fechaFinal'
						 AND co.claveEntidadFiscalCliente = $claveEF_Cliente");
		return $consulta;
    }

	public static function obtenerProductosCotizados($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente)
    {
    	$consulta = DB::connection('copico')->
    				select("
    					SELECT 
							cp.claveProducto
							, cd.codigoDeProducto
							, TRIM(Replace(Replace(Replace(cp.descripcion,'\t',''),'\n',''),'\r','')) AS descripcion
							, pv.precioUnitario
							, SUM(dc.cantidad) AS cantidad
							, (pv.precioUnitario * SUM(dc.cantidad)) AS total
						FROM cotizaciones co 
						JOIN comprobantes cm ON co.claveComprobanteDeCotizacion = cm.claveComprobante
						JOIN detallesdecomprobantes dc USING (claveComprobante)
						JOIN catalogodeproductos cp USING (claveProducto)
						JOIN codigosdeproductos cd USING (claveProducto)
						JOIN preciosdeventa pv ON cp.claveProducto = pv.claveProducto AND co.claveListaDePrecios = pv.claveListaDePrecios
						JOIN clientes ct USING (claveEntidadFiscalCliente)
						JOIN entidadesfiscales ef ON co.claveEntidadFiscalCliente = ef.claveEntidadFiscal
						JOIN tiposdestatusdecomprobantes tc USING (claveTipoDeStatusDeComprobante)
						WHERE tc.claveTipoDeStatusDeComprobante IN (160, 161, 162)
						AND cm.claveEntidadFiscalInmueble = $claveEF_Inmueble
						AND DATE(cm.fechaEmision) BETWEEN '$fechaInicio' AND '$fechaFinal'
						AND cd.claveTipoDeCodigoDeProducto = 1
						AND ct.claveEntidadFiscalCliente = $claveEF_Cliente
						GROUP BY cp.claveProducto, cd.codigoDeProducto
						ORDER BY total DESC
						LIMIT 10");
    	return $consulta;
    }

    public static function obtenerProductosVendidos($claveEF_Inmueble, $fechaInicio, $fechaFinal, $claveEF_Cliente)
    {
    	$consulta = DB::connection('copico')->
    				select("
    					SELECT 
							cp.claveProducto
							, cd.codigoDeProducto
							, TRIM(Replace(Replace(Replace(cp.descripcion,'\t',''),'\n',''),'\r','')) AS descripcion
							, CAST(pv.precioUnitario AS DECIMAL(64, 2)) AS precioUnitario
							, SUM(dc.cantidad) AS cantidad
							, CAST(pv.precioUnitario * SUM(dc.cantidad) AS DECIMAL(64, 2)) AS total
						FROM cotizaciones co 
						JOIN comprobantes cm ON co.claveComprobanteDeCotizacion = cm.claveComprobante
						JOIN detallesdecomprobantes dc USING (claveComprobante)
						JOIN catalogodeproductos cp USING (claveProducto)
						JOIN codigosdeproductos cd USING (claveProducto)
						JOIN preciosdeventa pv ON cp.claveProducto = pv.claveProducto AND co.claveListaDePrecios = pv.claveListaDePrecios
						JOIN clientes ct USING (claveEntidadFiscalCliente)
						JOIN entidadesfiscales ef ON co.claveEntidadFiscalCliente = ef.claveEntidadFiscal
						JOIN tiposdestatusdecomprobantes tc USING (claveTipoDeStatusDeComprobante)
						WHERE tc.claveTipoDeStatusDeComprobante = 164
						AND cm.claveEntidadFiscalInmueble = $claveEF_Inmueble
						AND DATE(cm.fechaEmision) BETWEEN '$fechaInicio' AND '$fechaFinal'
						AND cd.claveTipoDeCodigoDeProducto = 1
						AND ct.claveEntidadFiscalCliente = $claveEF_Cliente
						GROUP BY cp.claveProducto, cd.codigoDeProducto
						ORDER BY cantidad DESC
						LIMIT 10");
    	return $consulta;
    }
}