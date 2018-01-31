<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use PDO;
use PDF;

class Cotizacion extends Model
{
    protected $connection = 'copico';

    public static function ejecutarGuardar($empresa, $equipo, $usuario, $claveInmueble, $claveTipoDeComprobante, $fechaEmision, $partidas, $claveMoneda,
            $claveEntidadFiscalInmueble, $claveTipoEstatusRecepcion, $claveEntidadFiscalResponsable, $comprobantesImpuestos, $claveEntidadFiscalCliente,
            $ClaveListaDePrecios, $FechaVigencia, $SubTotal, $Descuento, $Impuestos, $Total, $Observaciones, $detallesComprobantes)
    {
        $error = null;        
        DB::beginTransaction();       
        try {
            $tipoOperacion = 'A';
            /* GENERA LOS CODIGOS(FOLIOS) COMPROBANTES */
            $sql = "CALL copicoods.foliosCodigosDeComprobantes_A(?, ?, ?, ?, ?, @_codigoDeComprobante)";
            DB::select($sql, array($empresa, $equipo, $usuario, $empresa, $claveTipoDeComprobante)); // retorna un array de objetos.                
            $claveComprobante = DB::select('SELECT @_codigoDeComprobante as folioCodigoComprobante');     
            
            $sql = "CALL copicoods.foliosComprobantes_A(?, ?, ?, ?, @_claveComprobante)";
            DB::select($sql, array($empresa, $equipo, $usuario, $empresa));
            //$claveComprobante = DB::select('SELECT @_claveComprobante as claveComprobante');
        
            $sql = "CALL copicoods.comprobantes_AC(?, ?, ?, @_claveComprobante, ?, ?, ?, ?, ?, ?, ?, @_codigoDeComprobante, @_result)";
            DB::select($sql, array(
                $empresa,
                $equipo,
                $usuario,
                $fechaEmision,
                $partidas,
                $claveTipoDeComprobante,
                $claveMoneda,
                $claveEntidadFiscalInmueble,
                $claveTipoEstatusRecepcion,
                $claveEntidadFiscalResponsable            
            ));

             /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
            if($claveTipoEstatusRecepcion == 162) {                
                $sql = "CALL sincronizador.comprobantes(@_claveComprobante, ?, ?, @_result)";
                DB::select($sql, array($claveEntidadFiscalInmueble, $tipoOperacion));                
            }
            /* IMPUESTOS */
            $array = json_decode($comprobantesImpuestos, true);                   
            foreach($array as $i => $row) {
                // $respuesta=$row['Importe'];                     
                $sql = "CALL copicoods.comprobantes_impuestos_AC(?, ?, ?, @_claveComprobante, ?, ?, @_result)";
                DB::select($sql, array($empresa, $equipo, $usuario, $row['ClaveImpuesto'], $row['Importe']));
                /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
                if($claveTipoEstatusRecepcion == 162) {
                    $sql = "CALL sincronizador.comprobantes_impuestos(@_claveComprobante, ?, ?, ?, @_result)";
                    DB::select($sql, array($row['ClaveImpuesto'], $claveEntidadFiscalInmueble, $tipoOperacion));
                }
            }
            /* COTIZACIONES */
            $sql = "CALL copicoods.cotizaciones_AC(?, ?, ?, @_claveComprobante, ?, ?, ?, ?, ?, ?, ?, ?, @_result)";
            DB::select($sql, array(                
                $empresa,
                $equipo,
                $usuario,                
                $claveEntidadFiscalCliente,
                $ClaveListaDePrecios,
                $FechaVigencia,
                $SubTotal,
                $Descuento,
                $Impuestos,
                $Total,
                $Observaciones
            ));
            /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
            if($claveTipoEstatusRecepcion == 162) {
                $sql = "CALL sincronizador.cotizaciones(@_claveComprobante, ?, ?, @_result)";
                DB::select($sql, array($claveEntidadFiscalInmueble, $tipoOperacion));
            }
            /* -------DETALLES DE COMPROBANTES---------- */
            $array= json_decode($detallesComprobantes,true);                                   
            foreach( $array as $i => $row)
            {
                $sql = "CALL copicoods.foliosDetallesDeComprobantes_A(?, ?, ?, ?, @_claveDetalleDeComprobante)";
                DB::select($sql, array($empresa, $equipo, $usuario, $empresa));
                $sql = "CALL copicoods.detallesDeComprobantes_AC(?, ?, ?, @_claveDetalleDeComprobante, ?, ?, ?, ?, ?, ?, ?, @_claveComprobante, @_result)";
                DB::select($sql,array(
                    $empresa,
                    $equipo,
                    $usuario,
                    $row['NumeroPartidas'],
                    $row['ClaveProducto'],
                    $row['Cantidad'],
                    $row['ClaveUnidadDeMedida'],
                    $row['PrecioUnitario'],
                    $row['Importe'],
                    $row['ImporteDescuento']                    
                ));                    
                 /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
                if($claveTipoEstatusRecepcion == 162) {
                    $sql="CALL sincronizador.detallesdecomprobantes(@_claveDetalleDeComprobante, ?, ?, @_result)";
                    DB::select($sql, array($claveEntidadFiscalInmueble, $tipoOperacion));
                }
                /* DETALLES DE COMPROBANTES_DIASDEENTREGA */
                $sql = "CALL copicoods.detallesdecomprobantes_diasdeentrega_AC(?, ?, ?, @_claveDetalleDeComprobante, ?, @_result)";
                DB::select($sql, array($empresa, $equipo, $usuario, $row['DiasDeEntrega']));
                /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
                if($claveTipoEstatusRecepcion == 162) {
                    $sql = "CALL sincronizador.detallesdecomprobantes_diasdeentrega(@_claveDetalleDeComprobante, ?, ?, @_result)";
                    DB::select($sql, array($claveEntidadFiscalInmueble, $tipoOperacion));
                }
                /* DETALLES COMPROBANTES_IMPUESTOS  */
                $array2 = json_decode($row["Impuestos"], true);
                foreach($array2 as $i2 => $v)
                {
                    $sql = "CALL copicoods.detallesdecomprobantes_impuestos_AC(?, ?, ?, @_claveDetalleDeComprobante, ?, ?, @_result)";
                    DB::select($sql, array($empresa, $equipo, $usuario, $v['ClaveImpuesto'], $v['Importe']));
                    /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
                    if($claveTipoEstatusRecepcion == 162) {                                            
                        $sql = "CALL sincronizador.detallesdecomprobantes_impuestos(@_claveDetalleDeComprobante, ?, ?, ?, @_result)";
                        DB::select($sql, array($v['ClaveImpuesto'], $claveEntidadFiscalInmueble, $tipoOperacion));
                    }
                }
            }
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            $error = $e->getMessage();
            DB::rollback();
        }
        if ($success) 
            return $claveComprobante;        
        else
            return $error;
    }

    public static function editarCotizacion($empresa, $equipo, $usuario, $claveInmueble, $claveTipoDeComprobante, $fechaEmision, $partidas, $claveMoneda,
        $claveEntidadFiscalInmueble, $claveTipoEstatusRecepcion, $claveEntidadFiscalResponsable, $ClaveComprobante, $codigoDeComprobante, $comprobantesImpuestos, 
        $claveEntidadFiscalCliente, $ClaveListaDePrecios, $FechaVigencia, $SubTotal, $Descuento, $Impuestos, $Total, $Observaciones, $detallesComprobantes)
    {              
        $error = null;
        DB::beginTransaction();
        try{
            $tipoOperacion = 'A';
            $sql = "CALL copicoods.comprobantes_AC(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @_result)";
            DB::select($sql, array(
                $empresa,
                $equipo,
                $usuario,
                $ClaveComprobante,
                $fechaEmision,
                $partidas,
                $claveTipoDeComprobante,
                $claveMoneda,
                $claveEntidadFiscalInmueble,
                $claveTipoEstatusRecepcion,
                $claveEntidadFiscalResponsable,
                $codigoDeComprobante            
            ));
            /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
            if($claveTipoEstatusRecepcion == 162) {                
                $sql = "CALL sincronizador.comprobantes(?, ?, ?, @_result)";
                DB::select($sql, array($ClaveComprobante, $claveEntidadFiscalInmueble, $tipoOperacion));
            }
            /* UPDATE COTIZACIONES */
            $sql="CALL copicoods.cotizaciones_AC(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @_result)";
            DB::select($sql, array(
                $empresa,
                $equipo,
                $usuario,
                $ClaveComprobante,
                $claveEntidadFiscalCliente,
                $ClaveListaDePrecios,
                $FechaVigencia,
                $SubTotal,
                $Descuento,
                $Impuestos,
                $Total,
                $Observaciones
            ));
             /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
            if($claveTipoEstatusRecepcion == 162) {
                $sql = "CALL sincronizador.cotizaciones(?, ?, ?, @_result)";
                DB::select($sql, array($ClaveComprobante, $claveEntidadFiscalInmueble, $tipoOperacion));
            }
            /* BORRAR COMPROBANTES_IMPUESTOS */ 
            $array = json_decode($comprobantesImpuestos, true);
            foreach($array as $i=> $row)
            {
                $sql = "CALL copicoods.comprobantes_impuestos_B(?, ?, ?, ?, ?, ?, @_result)";
                DB::select($sql, array(
                    $empresa,
                    $equipo,
                    $usuario,
                    $ClaveComprobante,
                    $row['ClaveImpuesto'],
                    $row['Importe']
                ));
            }
            /* ACTUALIZAR COMPROBANTES_IMPUESTOS */
            $array = json_decode($comprobantesImpuestos, true);
            foreach($array as $i => $row)
            {
                $sql="CALL copicoods.comprobantes_impuestos_AC(?,?,?,?,?,?,@_result)";
                DB::select($sql, array(
                    $empresa,
                    $equipo,
                    $usuario,
                    $ClaveComprobante,
                    $row['ClaveImpuesto'],
                    $row['Importe']
                ));
                /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
                if($claveTipoEstatusRecepcion == 162){                                            
                    $sql = "CALL sincronizador.comprobantes_impuestos(?, ?, ?, ?, @_result)";
                    DB::select($sql, array($ClaveComprobante, $row['ClaveImpuesto'], $claveEntidadFiscalInmueble, $tipoOperacion));
                }
            }                               
            //BORRAR DETALLES DE COMPROBANTES
            $array=json_decode($detallesComprobantes,true);
            foreach($array as $i => $row)
            {
                if($row['claveDetalleDeComprobante'])
                {
                    $sql="CALL copicoods.detallesDeComprobantes_B(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @_result)";
                    DB::select($sql, array(
                        $empresa,
                        $equipo,
                        $usuario,
                        $row['claveDetalleDeComprobante'],//AGREGAR CAMPO
                        $row['NumeroPartidas'],
                        $row['ClaveProducto'],
                        $row['Cantidad'],
                        $row['ClaveUnidadDeMedida'],
                        $row['PrecioUnitario'],
                        $row['Importe'],
                        $row['ImporteDescuento'],
                        $ClaveComprobante
                    ));
                    /* BORRA DIAS DE ENTREGA POR PARTIDA */
                    $sql="CALL copicoods.detallesdecomprobantes_diasdeentrega_B(?, ?, ?, ?, ?, @_result)";
                    DB::select($sql, [
                        $empresa,
                        $equipo,
                        $usuario,
                        $row['claveDetalleDeComprobante'],
                        $row['DiasDeEntrega']
                    ]);
                    /* DETALLES DE COMPROBANTES_IMPUESTOS */
                    $array2=json_decode($row['Impuestos'], true);
                    foreach($array2 as $i2 => $v)
                    {                            
                        $sql="CALL copicoods.detallesdecomprobantes_impuestos_B(?, ?, ?, ?, ?, ?, @_result)";
                        DB::select($sql, array(
                            $empresa,
                            $equipo,
                            $usuario,
                            $row['claveDetalleDeComprobante'],//AGREGAR CAMPO
                            $v['ClaveImpuesto'],
                            $v['Importe']
                        ));
                    }
                }
            }
            /* ACTUALIZAR DETALLES DE COMPROBANTES */ //IF($status==2)  nueva partida
            $array = json_decode($detallesComprobantes, true);
            foreach($array as $i => $row)
            {
                if($row['Estatus'] == 2 || $row['Estatus'] == 1 || $row['Estatus'] == 3)
                {                       
                    /*GENERAR FOLIO DETALLES DE COMPROBANTES */
                    $sql = "CALL copicoods.foliosDetallesDeComprobantes_A(?, ?, ?, ?, @_claveDetalleDeComprobante)";
                    DB::select($sql, array($empresa, $equipo, $usuario, $empresa));  
                    $sql="CALL copicoods.detallesDeComprobantes_AC(?, ?, ?, @_claveDetalleDeComprobante, ?, ?, ?, ?, ?, ?, ?, ?, @_result)";
                    DB::select($sql, array(
                        $empresa,
                        $equipo,
                        $usuario,                            
                        $row['NumeroPartidas'],
                        $row['ClaveProducto'],
                        $row['Cantidad'],
                        $row['ClaveUnidadDeMedida'],
                        $row['PrecioUnitario'],
                        $row['Importe'],
                        $row['ImporteDescuento'],
                        $ClaveComprobante
                    ));
                    /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
                    if($claveTipoEstatusRecepcion == 162) {
                        $sql="CALL sincronizador.detallesdecomprobantes(@_claveDetalleDeComprobante, ?, ?, @_result)";
                        DB::select($sql, array($claveEntidadFiscalInmueble, $tipoOperacion));
                    }
                    /* DETALLESDECOMPROBANTES_DIASDEENTREGA */
                    $sql="CALL copicoods.detallesdecomprobantes_diasdeentrega_AC(?, ?, ?, @_claveDetalleDeComprobante, ?, @_result)";
                    DB::select($sql, array($empresa, $equipo, $usuario, $row['DiasDeEntrega']));
                    /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
                    if($claveTipoEstatusRecepcion == 162){                               
                        $sql="CALL sincronizador.detallesdecomprobantes_diasdeentrega(@_claveDetalleDeComprobante, ?, ?, @_result)";
                        DB::select($sql, array($claveEntidadFiscalInmueble, $tipoOperacion));
                    }
                    /* ACTUALIZAR DETALLES COMPROBANTES IMPUESTOS */
                    $array2 = json_decode($row['Impuestos'], true);
                    foreach($array2 as $i2 => $v) {
                        $sql = "CALL copicoods.detallesdecomprobantes_impuestos_AC(?, ?, ?, @_claveDetalleDeComprobante, ?, ?, @_result)";
                        DB::select($sql, array(
                            $empresa,
                            $equipo,
                            $usuario,                                
                            $v['ClaveImpuesto'],
                            $v['Importe']
                        ));
                        /* SI LA COTIZACION ES EN DEFINITIVA SE PROCEDE A GUARDAR EN SINCRONIZADOR */
                        if($claveTipoEstatusRecepcion == 162){                                            
                            $sql="CALL sincronizador.detallesdecomprobantes_impuestos(@_claveDetalleDeComprobante, ?, ?, ?, @_result)";
                            DB::select($sql, array($v['ClaveImpuesto'], $claveEntidadFiscalInmueble, $tipoOperacion));
                        }
                    }
                }
            }
            DB::commit();
            $success = true;
        }catch(\Exception $e){
            $success = false;
            $error = $e->getMessage();
            DB::rollback();
        }
        if($success)        
            return 'yes';
        else
            return $error;
    }

    public static function mostrarCotizaciones($claveEF_Inmueble, $claveEF_Responsable, $fechaInicial, $fechaFinal, $txtCliente, $claveTipoEstatus)
    {
        $filtroFechas = "";
        $filtroEstatus = "";
        $filtroCliente = "";
        $filtroResponsable = "";        
        if($fechaInicial != null && $fechaFinal != null)
            $filtroFechas = " AND cm.fechaEmision BETWEEN '$fechaInicial' AND '$fechaFinal'";

        if($txtCliente != null)
            $filtroCliente = " AND (ct.codigoDeCliente LIKE '%$txtCliente%' OR ef.razonSocial LIKE '%$txtCliente%' OR ef.rfc LIKE '%$txtCliente%')";

        if($claveTipoEstatus != null)
            $filtroEstatus = " AND tc.claveTipoDeStatusDeComprobante = $claveTipoEstatus";

        if($claveEF_Responsable != null)
            $filtroResponsable= " AND cm.claveEntidadFiscalResponsable = $claveEF_Responsable";
        
        $consulta = DB::connection('copico')->
                    select("
                        SELECT co.claveComprobanteDeCotizacion, cm.codigoDeComprobante, co.claveEntidadFiscalCliente, cm.claveEntidadFiscalInmueble,
                        date_format(co.fechaVigencia, '%Y-%m-%d') as fechaVigencia, date_format(cm.fechaEmision, '%Y-%m-%d') as fechaEmision, 
                        ct.codigoDeCliente, ef.rfc, ef.razonSocial, ef.correoElectronico, cm.partidas, tc.descripcion AS estatus,  
                        tc.claveTipoDeStatusDeComprobante AS claveEstatus, co.subtotal, co.descuento, co.impuesto, co.total, co.observaciones
                        FROM cotizaciones co
                        JOIN comprobantes cm ON co.claveComprobanteDeCotizacion = cm.claveComprobante
                        JOIN clientes ct USING (claveEntidadFiscalCliente)
                        JOIN entidadesfiscales ef ON co.claveEntidadFiscalCliente = ef.claveEntidadFiscal
                        JOIN tiposdestatusdecomprobantes tc USING (claveTipoDeStatusDeComprobante)
                        WHERE tc.claveTipoDeStatusDeComprobante IN (160, 161, 162) AND
                        cm.claveEntidadFiscalInmueble = $claveEF_Inmueble
                        $filtroResponsable
                        $filtroFechas
                        $filtroCliente
                        $filtroEstatus");
        return $consulta;
    }

    public static function cargarDetallesCotizacion($claveComprobante)
    {
        $consulta = DB::connection('copico')->
                    select("
                        SELECT d.claveDetalleDeComprobante, d.claveProducto, p.descripcion, b.esImportado, x.sumaimpuestos, x.tasas, x.clavesImpuestos,
                        c.codigoDeProducto as codigoInterno, ROUND(d.precioUnitario, 2) AS precioUnitario,  ROUND(d.cantidad, 2) as cantidad, 
                        ROUND(d.importeDescuento, 2) AS importeDescuento, ROUND(d.importe, 2) AS importe, 
                        ROUND((d.importe - d.importeDescuento) * x.sumaimpuestos/100, 2) AS impuestos, 
                        ROUND((d.importe - d.importeDescuento) + ((d.importe - d.importeDescuento) * x.sumaimpuestos/100), 2) AS subtotal,
                        IFNULL(e.diasDeEntrega, 0) AS diasdeentrega
                        FROM detallesdecomprobantes d
                        JOIN bienes b ON d.claveProducto = b.claveProducto
                        JOIN catalogodeproductos p ON d.claveProducto = p.claveProducto
                        JOIN impuestos_catalogodeproductos i ON p.claveProducto = i.claveProducto
                        JOIN codigosdeproductos c ON p.claveProducto = c.claveProducto AND c.claveTipoDeCodigoDeProducto = 1
                        LEFT JOIN detallesdecomprobantes_diasdeentrega e ON d.claveDetalleDeComprobante = e.claveDetalleDeComprobante
                        JOIN (
                            SELECT p.claveProducto, SUM(c.tasa) AS sumaimpuestos, group_concat(c.tasa) as tasas, group_concat(c.claveImpuesto) as clavesImpuestos
                            FROM catalogodeproductos p
                            JOIN impuestos_catalogodeproductos i ON p.claveProducto = i.claveProducto
                            JOIN catalogodeimpuestos c ON i.claveImpuesto = c.claveImpuesto
                            GROUP BY p.claveProducto) x ON p.claveProducto = x.claveProducto
                        WHERE claveComprobante = $claveComprobante");
        return $consulta;
    }

    public static function descargarPDF($claveEF_Empresa, $codigoComprobante)
    {        
        $sql = "SELECT c.codigoDeComprobante, DATE(c.fechaEmision) AS fechaEmision, ctz.fechaVigencia, cliente.codigoDeCliente, 
                ctz.subtotal, ctz.descuento, ctz.impuesto, ctz.total, ctz.observaciones, c.partidas, 
                CONCAT(df.calle,' #', df.numeroExterior, ', Col. ', df.colonia, ', ', df.localidad, ', ', ' ', e.nombre, ', C.P. ', df.codigoPostal) as direccion,
                CONCAT('TEL: (', tel.codigoDeZona,') ', tel.numero) AS telefono, ef.razonSocial AS cliente, ci.importe AS total_impuestos
                FROM cotizaciones AS ctz
                LEFT JOIN comprobantes AS c ON ctz.claveComprobanteDeCotizacion = c.claveComprobante
                LEFT JOIN clientes AS cliente ON cliente.claveEntidadFiscalCliente = ctz.claveEntidadFiscalCliente
                LEFT JOIN entidadesfiscales AS ef ON ef.claveEntidadFiscal = cliente.claveEntidadFiscalCliente
                LEFT JOIN inmuebles AS i ON i.claveEntidadFiscalInmueble = c.claveEntidadFiscalInmueble AND i.claveEntidadFiscalEmpresa = $claveEF_Empresa
                LEFT JOIN entidadesfiscales AS efdir ON efdir.claveEntidadFiscal = i.claveEntidadFiscalInmueble
                LEFT JOIN direccionesfiscales AS df ON df.claveEntidadFiscal = efdir.claveEntidadFiscal
                LEFT JOIN estados AS e ON df.claveEstado = e.claveEstado
                LEFT JOIN telefonos AS tel ON tel.claveEntidadFiscal = $claveEF_Empresa
                LEFT JOIN comprobantes_impuestos AS ci ON ci.claveComprobante = c.claveComprobante
                WHERE c.codigoDeComprobante = ?";
        $comprobantes = DB::connection('copico')->select($sql, array($codigoComprobante));

        $clave = Comprobantes::where('codigoDeComprobante', $codigoComprobante)->first();

        $sql = "SELECT dc.numeroDePartida, cdp.codigoDeProducto, cp.descripcion, dc.cantidad, um.descripcion AS UnidadMedida, 
                dc.precioUnitario, dc.importe, dc.importeDescuento, dci.claveImpuesto, dci.importe AS impuestos
                FROM detallesdecomprobantes AS dc
                LEFT JOIN catalogodeproductos AS cp ON cp.claveProducto = dc.claveProducto
                LEFT JOIN codigosdeproductos AS cdp ON cdp.claveProducto = dc.claveProducto AND cdp.claveTipoDeCodigoDeProducto = 1
                LEFT JOIN unidadesdemedida AS um ON dc.claveUnidadDeMedida = um.claveUnidadDeMedida
                LEFT JOIN detallesdecomprobantes_impuestos AS dci ON dci.claveDetalleDeComprobante = dc.claveDetalleDeComprobante
                WHERE dc.claveComprobante = ?";
        $detallesComprobantes =  DB::connection('copico')->select($sql, array($clave->claveComprobante));

        /* DIAS DE ENTREGA */
        $diasDeEntrega = DB::connection('copico')
                            ->select("
                                SELECT MAX(dcde.diasDeEntrega) AS diasDeEntrega 
                                FROM detallesdecomprobantes AS dc
                                LEFT JOIN detallesdecomprobantes_diasdeentrega AS dcde ON dc.claveDetalleDeComprobante = dcde.claveDetalleDeComprobante
                                WHERE claveComprobante = $clave->claveComprobante");

        $sumarDias = $diasDeEntrega[0]->diasDeEntrega;
        $date = $comprobantes[0]->fechaEmision;
        //Incrementando dias
        $mod_date = strtotime($date."+ ".$sumarDias."days");
        $fechaEntrega = date('Y-m-d',$mod_date);    

        /*CONDICIONES COMERCIALES*/
        $condComCTZ = CondicionesComercialesCtz::where('claveEntidadFiscalEmpresa', $claveEF_Empresa)->first();        
        $condComercial = explode("\n", $condComCTZ->condicionComercial);

        /*GENERA EL PDF*/
        $pdf = PDF::loadView('pdfView', compact('detallesComprobantes', 'comprobantes', 'condComercial', 'fechaEntrega'));
        return $pdf;
    }
}
