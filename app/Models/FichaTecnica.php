<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class FichaTecnica extends Model
{
    protected $connection = 'copico';

    public static function buscarFichaTecnica($claveProducto)
    {
    	$consulta = DB::connection('copico')->
    				select("
    					SELECT 
                            f.claveProducto
                            , f.resumen
                            , to_base64(i.contenido) AS imagen
                            , p.descripcion, o.codigoDeProducto
                            , i.extension
						FROM fichatecnica f
						JOIN catalogodeproductos_imagenes c ON f.claveProducto=c.claveProducto
						JOIN imagenes i ON c.claveImagen=i.claveImagen
						JOIN catalogodeproductos p ON f.claveProducto=p.claveProducto
						JOIN codigosdeproductos o ON f.claveProducto=o.claveProducto
						WHERE o.claveTipoDeCodigoDeProducto = 1
						AND f.claveProducto = $claveProducto");
    	return $consulta;
    }
}