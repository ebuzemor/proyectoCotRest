<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EntidadesFiscales;
use DB;

class Clientes extends Model
{
    protected $connection = 'copico';

    protected $table = 'clientes';

    public static function buscarClientes($ClaveEF_Empresa, $txtBusqueda)
    {
        $entidadFiscal = EntidadesFiscales::find($ClaveEF_Empresa);        
        $consulta = DB::connection('copico')
                        ->select("SELECT 
                                  c.claveEntidadFiscalCliente
                                  , c.claveEntidadFiscalEmpresa
                                  , tc.claveTipoDeCliente
                                  , c.codigoDeCliente
                                  , tc.nombre AS tipoDeCliente 
                                  , ef.personaFisica
                                  , cc.claveClasificador
                                  , cc.descripcion AS clasificador
                                  , ef.razonSocial
                                  , ef.rfc
                                  , ef.correoElectronico
                                  , df.municipio
                                  , df.claveEstado
                                  , es.nombre AS estado
                                  , p.clavePais
                                  , p.nombre AS pais
                                  , df.codigoPostal
                                  , df.claveDireccionFiscal
                                  , df.calle
                                  , df.numeroExterior
                                  , df.numeroInterior
                                  , df.colonia
                                  , df.localidad
                                  , IFNULL(cv.claveEntidadFiscalVendedor, 0) AS claveEntidadFiscalVendedor
                                  , IFNULL(cco.claveEntidadFiscalCobrador, 0) AS claveEntidadFiscalCobrador
                                  , CONCAT(df.calle, ' No.', df.numeroExterior, ', ', df.colonia, ' Cp: ', df.codigoPostal) AS direccion
                                  , c.esEspecial AS especial, GROUP_CONCAT(DISTINCT(con.nombre)) AS Contacto
                                  , GROUP_CONCAT(DISTINCT(CONCAT(tel.codigoDePais, '-', tel.codigoDeZona, '-', tel.numero
                                  , CASE tel.esCelular WHEN tel.esCelular = 1 THEN ' (Cel.)' ELSE CONCAT(' ext.', tel.extension) END))) AS NumeroTelefono
                                  FROM clientes AS c
                                  LEFT JOIN tiposDeClientes AS tc ON(c.claveTipoDeCliente = tc.claveTipoDeCliente)
                                  LEFT JOIN clientes_clasificadoresDeClientes AS c_cc ON(c.claveEntidadFiscalCliente = c_cc.claveEntidadFiscalCliente)
                                  LEFT JOIN clasificadoresDeClientes AS cc ON(c_cc.claveClasificador = cc.claveClasificador)
                                  LEFT JOIN entidadesFiscales AS ef ON(c.claveEntidadFiscalCliente = ef.claveEntidadFiscal)
                                  LEFT JOIN direccionesFiscales AS df ON(ef.claveEntidadFiscal = df.claveEntidadFiscal)
                                  LEFT JOIN estados AS es ON(df.claveEstado = es.claveEstado)
                                  LEFT JOIN paises AS p ON(es.clavePais = p.clavePais)
                                  LEFT JOIN clientes_vendedores AS cv ON(ef.claveEntidadFiscal = cv.claveEntidadFiscalCliente)
                                  LEFT JOIN clientes_cobradores AS cco ON(c.claveEntidadFiscalCliente = cco.claveEntidadFiscalCliente)
                                  LEFT JOIN contactos AS con ON c.claveEntidadFiscalCliente = con.claveEntidadFiscalCliente
                                  LEFT JOIN telefonos AS tel ON c.claveEntidadFiscalCliente = tel.claveEntidadFiscal
                                  WHERE c.claveEntidadFiscalEmpresa = $ClaveEF_Empresa AND ef.rfc != '$entidadFiscal->rfc' 
                                  AND (c.codigoDeCliente LIKE '%$txtBusqueda%' OR ef.razonSocial LIKE '%$txtBusqueda%' OR ef.rfc LIKE '%$txtBusqueda%' 
                                       OR ef.correoElectronico LIKE '%$txtBusqueda%')
                                  GROUP BY c.claveEntidadFiscalCliente, tc.nombre, cc.claveClasificador, cv.claveEntidadFiscalVendedor, cco.claveEntidadFiscalCobrador");
        return $consulta;
    }

    public static function guardarCliente($empresa, $equipo, $usuario, $claveInmueble, $razonsocial, $rfc, $correoElectronico, $personaFisica, $claveEF_Empresa, 
                                          $calle, $numeroExterior, $numeroInterior, $colonia, $localidad, $municipio, $claveEstado, $clavePais, $codigoPostal,
                                          $claveTipoDeCliente, $esEspecial, $claveEntidadFiscalVendedor, $claveClasificador, $listaDeContactos, $listaDeTelefonos) {
        $error = null;
        DB::beginTransaction();
        try {
            $tipoOperacion = 'A';

            //SI ES UN CLIENTE NUEVO, DAMOS DE ALTA SU ENTIDAD FISCAL Y/O FOLIOS
            /* ---------------GENERAMOS FOLIOS----------------- */
            $sql="CALL copicoods.folios_entidadesFiscales_A(?,?,?,?,@_claveEntidadFiscalCliente)";
            DB::select($sql,array($usuario,$equipo,$empresa,$claveInmueble));
            $claveEF_Cliente = DB::select('SELECT @_claveEntidadFiscalCliente as claveEntidadFiscalCliente');     

            $sql="CALL copicoods.foliosDeDireccionesFiscales_A(?,@_claveDireccionFiscal)";
            DB::select($sql,array($claveInmueble));

            $sql="CALL copicoods.foliosDeCodigosDeClientes_A(?,@_codigoDeCliente)";
            DB::select($sql,array($claveInmueble));

            /* GUARDAR EN ENTIDADESFISCALES  */
            $sql="CALL copicoods.entidadesFiscales_AC(?,?,?,@_claveEntidadFiscalCliente,?,?,?,?,@_result)";
            DB::select($sql,array($empresa,$equipo,$usuario,$razonsocial,$rfc,$correoElectronico,$personaFisica));

            // GUARDAR EN SINCRONIZADOR
            $sql="CALL sincronizador.entidadesFiscalesClientes_A(?,@_claveEntidadFiscalCliente,?,@_result)";
            DB::select($sql,array($claveEF_Empresa,$tipoOperacion));

            $sql="CALL copicoods.direccionesFiscales_AC(?,?,?,@_claveDireccionFiscal,?,?,?,?,?,?,?,?,?,@_claveEntidadFiscalCliente,@_result)";
            DB::select($sql,array(
                $empresa,
                $equipo,
                $usuario,
                $calle,
                $numeroExterior,
                $numeroInterior,
                $colonia,
                $localidad,
                $municipio,
                $claveEstado,
                $clavePais,
                $codigoPostal                                            
            ));
             // GUARDAR EN SINCRONIZADOR
            $sql="CALL sincronizador.direccionesFiscalesClientes_A(?,@_claveDireccionFiscal,?,@_result)";
            DB::select($sql,array($claveEF_Empresa,$tipoOperacion));

            $sql="CALL copicoods.clientes_AC(?,?,?,@_claveEntidadFiscalCliente,?,@_codigoDeCliente,?,?,@_result)";
            DB::select($sql,array(
                $empresa,
                $equipo,
                $usuario,
                $claveEF_Empresa,
                $claveTipoDeCliente,
                $esEspecial
            ));
             // GUARDAR EN SINCRONIZADOR
            $sql="CALL sincronizador.clientes_A(@_claveEntidadFiscalCliente,?,?,@_result)";
            DB::select($sql,array($claveEF_Empresa,$tipoOperacion));

            /* CLIENTES_VENDEDORES  */
            // OPCIONAL            
            $sql="CALL copicoods.clientes_vendedores_A(?,?,?,@_claveEntidadFiscalCliente,?,@_result)";
            DB::select($sql,array(
                $empresa,
                $equipo,
                $usuario,
                $claveEntidadFiscalVendedor
            ));

            // SINCRONIZADOR CLIENTES_vENDEDORES
            // OPCIONAL
            $sql="CALL sincronizador.clientes_vendedores_A(?,@_claveEntidadFiscalCliente,?,?,@_result)";
            DB::select($sql,array($claveEF_Empresa,$claveEntidadFiscalVendedor,$tipoOperacion));

            /* CLIENTES CLASIFICADORES DE CLIENTES */
            $sql="CALL copicoods.clientes_clasificadoresDeClientes_A(?,?,?,@_claveEntidadFiscalCliente,?,@_result)";
            DB::select($sql,array(
                $empresa,
                $equipo,
                $usuario,
                $claveClasificador
            ));

            // SINCRONIZADOR CLASIFICADORES DE CLIENTES
            $sql="CALL sincronizador.clientes_clasificadoresDeClientes_A(?,@_claveEntidadFiscalCliente,?,?,@_result)";
            DB::select($sql,array($claveEF_Empresa,$claveClasificador,$tipoOperacion));

            /*  LISTA DE CONTACTOS */
            $array= json_decode($listaDeContactos,true);      
            foreach( $array as $i => $row)
            {
                // GENERAMOS LOS FOLIOS DE CONTACTO
                $sql="CALL copicoods.foliosDeContactos_A(@_claveEntidadFiscalCliente,?,@_folioContacto)";
                DB::select($sql,array($claveInmueble));
                //$tipoMovimiento="A";                    

                $sql="CALL copicoods.contactos_AC(?,?,?,@_claveEntidadFiscalCliente,@_folioContacto,?,?,@_result)";
                DB::select($sql,array(
                    $empresa,
                    $equipo,
                    $usuario,                    
                    $row['nombre'],
                    $row['observaciones']
                ));

                $sql="CALL sincronizador.contactos_A(?,@_claveEntidadFiscalCliente,@_folioContacto,?,@_result)";
                DB::select($sql,array($claveEF_Empresa,$tipoOperacion));                                
            }            

            /* LISTA DE TELEFONOS */
            $array= json_decode($listaDeTelefonos,true);      
            foreach( $array as $i => $row)
            {                
                $sql="CALL copicoods.foliosDeTelefonos_A(@_claveEntidadFiscalCliente,?,@_folioTelefono)";
                DB::select($sql,array($claveInmueble));
                //$tipoMovimiento="A";                

                $sql="CALL copicoods.telefonos_AC(?,?,?,@_claveEntidadFiscalCliente,@_folioTelefono,?,?,?,?,?,@_result)";
                DB::select($sql,array(
                    $empresa,
                    $equipo,
                    $usuario,
                    $row['codigoDePais'],
                    $row['codigoDeZona'],
                    $row['numero'],
                    $row['extension'],
                    $row['esCelular']
                ));

                $sql="CALL sincronizador.telefonos_A(?,@_claveEntidadFiscalCliente,@_folioTelefono,?,@_result)";
                DB::select($sql,array($claveEF_Empresa,$tipoOperacion));
            }

            DB::commit();
            $success = true;
        }catch(\Exception $e){
            $success = false;
            $error = false; //$e->getMessage();
            DB::rollback();
        }

        if ($success){
            //success
            return $claveEF_Cliente;
        }
        else{
            return $error;
            //error  -testing-            
        }        
    }

    public static function verificarClienteEmail($correoElectronico) {
        $consulta = DB::connection('copico')
                        ->select("
                            SELECT c.claveEntidadFiscalCliente FROM clientes AS c
                               LEFT JOIN entidadesfiscales AS ef
                               ON c.claveEntidadFiscalCliente=ef.claveEntidadFiscal
                            WHERE c.claveEntidadFiscalEmpresa=100000205 AND ef.correoElectronico='$correoElectronico'
                        ");
        return $consulta;
    }
}