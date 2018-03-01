<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Productos extends Model
{
    protected $connection = 'copico';

    protected $table = 'catalogodeproductos';    

    public static function buscarProductos($claveEF_Inmueble, $claveEF_Cliente, $txtBusqueda)
    {
    	$claveComPagoTarjeta = 100000005;
    	$consulta = DB::connection('copico')->
    				select("
						SELECT SQL_CALC_FOUND_ROWS
							produc_exis . *
							, GROUP_CONCAT(DISTINCT agr_cp.claveAgrupador ORDER BY agr_cp.claveAgrupador) AS clavesAgrupadores
							, GROUP_CONCAT(DISTINCT agr.descripcion ORDER BY agr.claveAgrupador) AS agrupadores
							, GROUP_CONCAT(DISTINCT agr.claveAgrupadorPadre ORDER BY agr.claveAgrupador) AS clavesAgrupadoresPadre
							, GROUP_CONCAT(DISTINCT agrp.descripcion) AS agrupadoresPadre
							, cosb.costobase
							, IFNULL(pck.existenciaPack, 0) AS existenciaPack
							, IFNULL(pck.claveProductoPieza, 0) AS claveProductoPieza
							, IFNULL(pck.cantidadPieza, 1) AS cantidadPieza
						FROM
						 	(SELECT 
								produc . *
								, tp.descripcion AS tipoDeProducto
								, b.cantidadMinimaDeCompra
								, b.cantidadMinimaDeTraslado
								, b.cantidadMinimaDeVenta
								, b.esFraccionable
								, b.esImportado
								, b.esParte
								, b.estaSeriado
								, b.requiereDatosDeImportacion
								, b.claveUnidadDeMedida
								, um.descripcion AS unidadDeMedida
								, um.abreviatura
								, IFNULL(cpr.claveTipoDeRedondeo, 0) AS claveTipoDeRedondeo
								, IFNULL(pr.precisionDeRedondeo, 0) AS precisionDeRedondeo, SUM(IFNULL(mi.cantidad, 0)) AS existencia
							FROM
						 		(SELECT 
									pvi_imp_precios . *
									, pb.peso, CONCAT(pb.peso, ' ', umpb.abreviatura) AS pesoYUnidad
									, umpb.descripcion AS unidadDeMedidaPeso
									, umpb.abreviatura AS abreviaturaPeso
									, codpIn.codigoDeProducto AS codigoInterno
									, codpPr.codigoDeProducto AS codigoDeProveedor
									, codpAn.codigoDeProducto AS codigoAnterior
									, codpCB.codigoDeProducto AS codigoDeBarras
								FROM
						 			(SELECT 
										pvi_imp.*
										, cpt.claveCuentaBancaria
										, cpt.tasa,cpt.mensualidad
										, GROUP_CONCAT(ci.claveImpuesto) AS clavesImpuestos
										, GROUP_CONCAT(ci.descripcion) AS descripcionImpuestos
										, GROUP_CONCAT(ci.tasa) AS tasas
										, SUM(ci.tasa) AS sumaImpuestos
										, cpt.tasa AS comisionDePagoConTarjeta
										, CAST(((1 + (0 / 100)) * pvi_imp.precioUnitario) AS DECIMAL (64, 2)) AS precioCom
										, SUM(CAST((ci.tasa / 100) * pvi_imp.precioUnitario AS DECIMAL (64, 2))) + pvi_imp.precioUnitario AS precioPublico
										, SUM(CAST((ci.tasa / 100) * CAST(((1 + (0 / 100)) * pvi_imp.precioUnitario) AS DECIMAL (64, 2)) 
										  AS DECIMAL (64, 2))) + CAST(((1 + (0 / 100)) * pvi_imp.precioUnitario) AS DECIMAL (64, 2)) AS precioPublicoCom
									FROM
										(SELECT 
											pvi . *
											, pdv.claveListaDePrecios, IFNULL(claveListaDePreciosCliente, - 1) AS claveListaDePreciosCliente
											, CAST(pdv.precioUnitario AS DECIMAL (64, 2)) AS precioUnitario
											, pdv.claveMoneda 
											, m.descripcion AS moneda 
										FROM 
											(SELECT 
												cp.claveProducto
												, TRIM(Replace(Replace(Replace(cp.descripcion,'\t',''),'\n',''),'\r','')) AS descripcion
												, cp.claveTipoDeProducto
												, -1 AS claveFraccionReenvasable
												, i.claveEntidadFiscalInmueble
												, cpe.claveEntidadFiscalEmpresa
											FROM CatalogoDeProductos_Empresas AS cpe
											INNER JOIN Inmuebles AS i USING (claveEntidadFiscalEmpresa)
											INNER JOIN CatalogoDeProductos AS cp USING (claveProducto)
											INNER JOIN ProductosVendibles AS pv USING (claveProducto, claveEntidadFiscalInmueble)
											WHERE 
												i.claveEntidadFiscalInmueble = $claveEF_Inmueble) AS pvi
										LEFT JOIN PreciosDeVenta AS pdv USING (claveProducto)
										INNER JOIN (SELECT 
														lp.claveListaDePrecios
														, lpc.claveListaDePrecios AS claveListaDePreciosCliente
													FROM listasdeprecios AS lp
													LEFT JOIN listasdeprecios_clientes AS lpc 
														ON (lpc.claveListaDePrecios = lp.claveListaDePrecios AND lpc.claveEntidadFiscalCliente = $claveEF_Cliente)
													LEFT JOIN listasdeprecios_sucursales AS lps 
														ON (lps.claveListaDePrecios = lp.claveListaDePrecios AND lps.claveEntidadFiscalInmueble = $claveEF_Inmueble)
													WHERE 
														(lps.claveListaDePrecios = 1) 
													ORDER BY lpc.claveEntidadFiscalCliente DESC 
													LIMIT 1) AS lp USING (claveListaDePrecios)
										INNER JOIN Monedas AS m USING (claveMoneda)) AS pvi_imp
										INNER JOIN impuestos_catalogodeproductos AS icp USING (claveProducto)
										INNER JOIN CatalogoDeImpuestos AS ci USING (claveImpuesto)
										INNER JOIN CuentasBancarias_Empresas USING (claveEntidadFiscalEmpresa)
										INNER JOIN ComisionesDePagosConTarjeta AS cpt USING (claveCuentaBancaria)										
										WHERE
											cpt.claveComisionDePagoConTarjeta = $claveComPagoTarjeta  
										GROUP BY claveProducto, pvi_imp.claveEntidadFiscalEmpresa, pvi_imp.claveListaDePrecios) AS pvi_imp_precios
									LEFT JOIN PesosDeBienes AS pb USING (claveProducto)
									LEFT JOIN UnidadesDeMedida AS umpb ON umpb.claveUnidadDeMedida = pb.claveUnidadDeMedida
									LEFT JOIN Agrupadores_CatalogoDeProductos AS agr_cp USING (claveProducto)
									LEFT JOIN Agrupadores AS agr USING (claveAgrupador) 
									LEFT JOIN CodigosDeProductos AS codpIn 
										ON (codpIn.claveProducto = pvi_imp_precios.claveProducto AND codpIn.claveTipoDeCodigoDeProducto = 1) 
									LEFT JOIN CodigosDeProductos AS codpPr 
										ON (codpPr.claveProducto = pvi_imp_precios.claveProducto AND codpPr.claveTipoDeCodigoDeProducto = 2)
									LEFT JOIN CodigosDeProductos AS codpAn 
										ON (codpAn.claveProducto = pvi_imp_precios.claveProducto AND codpAn.claveTipoDeCodigoDeProducto = 3)
									LEFT JOIN CodigosDeProductos AS codpCB 
										ON (codpCB.claveProducto = pvi_imp_precios.claveProducto AND codpCB.claveTipoDeCodigoDeProducto = 4) 
									WHERE 
										pvi_imp_precios.descripcion LIKE '%$txtBusqueda%'
										OR codpIn.codigoDeProducto LIKE '%$txtBusqueda%'
										OR codpPr.codigoDeProducto LIKE '%$txtBusqueda%'
										OR codpAn.codigoDeProducto LIKE '%$txtBusqueda%'
										OR codpCB.codigoDeProducto LIKE '%$txtBusqueda%'
										OR agr.descripcion LIKE '%$txtBusqueda%'
									GROUP BY pvi_imp_precios.claveProducto, pvi_imp_precios.claveEntidadFiscalEmpresa,pvi_imp_precios.claveListaDePrecios,
										codpIn.codigoDeProducto, codpPr.codigoDeProducto, codpAn.codigoDeProducto, codpCB.codigoDeProducto) AS produc
								LEFT JOIN MovimientosDeInventarios AS mi USING (claveProducto, claveEntidadFiscalInmueble)
								INNER JOIN TiposDeProductos AS tp USING (claveTipoDeProducto)
								INNER JOIN Bienes AS b USING (claveProducto)
								INNER JOIN UnidadesDeMedida AS um ON um.claveUnidadDeMedida = b.claveUnidadDeMedida
								LEFT JOIN CatalogoDeProductos_Redondeos AS cpr USING (claveProducto)
								LEFT JOIN PrecisionesDeRedondeo AS pr USING (clavePrecisionDeRedondeo)
								GROUP BY produc.claveProducto, produc.claveEntidadFiscalEmpresa, produc.claveListaDePrecios,
									produc.codigoInterno, produc.codigoDeProveedor, produc.codigoAnterior, produc.codigoDeBarras) AS produc_exis
							LEFT JOIN Agrupadores_CatalogoDeProductos AS agr_cp USING (claveProducto)
							LEFT JOIN Agrupadores AS agr USING (claveAgrupador)
							LEFT JOIN Agrupadores AS agrp ON agrp.claveAgrupador = agr.claveAgrupadorPadre
							LEFT JOIN costobase AS cosb ON agr_cp.claveProducto = cosb.claveProducto
							LEFT JOIN
								(SELECT
									p.clavePack AS clavePack
									, ROUND(SUM(IFNULL(mi.cantidad, 0)) / IFNULL(p.cantidad, 1), 4) AS existenciaPack
									, IFNULL(p.claveProducto, 0) AS claveProductoPieza, IFNULL(p.cantidad, 1) AS cantidadPieza
								 FROM packs AS p
								 LEFT JOIN movimientosdeinventarios AS mi ON (p.claveProducto = mi.claveProducto)
								 WHERE
									mi.claveEntidadFiscalInmueble = $claveEF_Inmueble
								GROUP BY p.claveProducto, mi.claveProducto, p.cantidad) AS pck 
								ON (produc_exis.claveProducto = pck.clavePack)
							GROUP BY produc_exis.claveProducto, produc_exis.claveEntidadFiscalEmpresa, produc_exis.claveListaDePrecios,
								produc_exis.codigoInterno, produc_exis.codigoDeProveedor, produc_exis.codigoAnterior, 
								produc_exis.codigoDeBarras, cosb.costoBase, pck.existenciaPack, pck.claveProductoPieza, pck.cantidadPieza
						");
		return $consulta;
	}

	public static function buscarExistencias($claveEF_Empresa, $claveProducto)
	{
		$consulta = DB::connection('copico')->
    				select("
    					SELECT IFNULL(p.claveProducto, 'no hay') AS claveProducto
						, IFNULL(p.descripcion, 'no hay') AS descripcion
						, SUM(COALESCE(CAST(m.cantidad AS DECIMAL(10,2)), 0)) AS cantidad
						, IFNULL(i.nombreCorto, 'no hay') AS sucursal
						FROM catalogodeproductos p
						LEFT JOIN movimientosdeinventarios m ON p.claveProducto = m.claveProducto
						LEFT JOIN inmuebles i ON m.claveEntidadFiscalInmueble = i.claveEntidadFiscalInmueble
						WHERE i.claveEntidadFiscalEmpresa = $claveEF_Empresa AND p.claveProducto = $claveProducto
						AND i.codigoDeInmueble != 3001
						GROUP BY i.nombreCorto
    					");
    	return $consulta;
	}

	public static function sincronizarCatalogo($claveEF_Inmueble)
	{
		$filtroInmueble = "";
		if($claveEF_Inmueble != null && $claveEF_Inmueble != 0)
			$filtroInmueble = " WHERE m.claveEntidadFiscalInmueble = $claveEF_Inmueble";

		$consulta = DB::connection('copico')->
					select("
						SELECT c.claveProducto AS sku
						, CAST(COALESCE(SUM(m.cantidad)/(SELECT COUNT(*) FROM listasdeprecios), 0) AS DECIMAL(64,2)) AS qty
						, MAX(COALESCE(CASE WHEN l.claveListaDePrecios = 1 THEN CAST(p.precioUnitario AS DECIMAL(64,2)) END, 0)) AS price
						, MAX(COALESCE(CASE WHEN l.claveListaDePrecios = 2 THEN CAST(p.precioUnitario AS DECIMAL(64,2)) END, 0)) AS 'group_price:Lista 2'
						, MAX(COALESCE(CASE WHEN l.claveListaDePrecios = 3 THEN CAST(p.precioUnitario AS DECIMAL(64,2)) END, 0)) AS 'group_price:Lista 3'
						, MAX(COALESCE(CASE WHEN l.claveListaDePrecios = 4 THEN CAST(p.precioUnitario AS DECIMAL(64,2)) END, 0)) AS 'group_price:Lista 4'
						, MAX(COALESCE(CASE WHEN l.claveListaDePrecios = 5 THEN CAST(p.precioUnitario AS DECIMAL(64,2)) END, 0)) AS 'group_price:Lista 5'
						, MAX(COALESCE(CASE WHEN l.claveListaDePrecios = 6 THEN CAST(p.precioUnitario AS DECIMAL(64,2)) END, 0)) AS 'group_price:Lista 6'
						, MAX(COALESCE(CASE WHEN l.claveListaDePrecios = 7 THEN CAST(p.precioUnitario AS DECIMAL(64,2)) END, 0)) AS 'group_price:Lista 7'
						, COALESCE(b.use_config_backorders, 0) AS use_config_backorders
						, COALESCE(v.status, 0) AS status
						FROM catalogodeproductos c
						LEFT JOIN movimientosdeinventarios m ON c.claveProducto = m.claveProducto
						LEFT JOIN preciosdeventa p ON c.claveProducto = p.claveProducto
						LEFT JOIN listasdeprecios l ON p.claveListaDePrecios = l.claveListaDePrecios
						LEFT JOIN backordersdeproductos_web b ON c.claveProducto = b.claveproducto
						LEFT JOIN visibilidaddeproductos_web v ON c.claveProducto = v.claveproducto
						$filtroInmueble
						GROUP BY c.claveProducto
						");
		return $consulta;
	}
}