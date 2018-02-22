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
                                  AND (c.codigoDeCliente LIKE '%$txtBusqueda%' OR ef.razonSocial LIKE '%$txtBusqueda%' OR ef.rfc LIKE '%$txtBusqueda%')
                                  GROUP BY c.claveEntidadFiscalCliente, tc.nombre, cc.claveClasificador, cv.claveEntidadFiscalVendedor, cco.claveEntidadFiscalCobrador");
        return $consulta;
    }
}