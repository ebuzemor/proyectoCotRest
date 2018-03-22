<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Productos;
use App\MyClass\ApiStatus;
use Excel;
use Storage;
use Illuminate\Http\File;

class ProductosController extends Controller
{
    function __construct()
    {
    	$this->middleware('api');
    }

    public function buscarProductos($claveEF_Inmueble, $claveEF_Cliente, $txtBusqueda)
    {
    	$buscarProductos = Productos::buscarProductos($claveEF_Inmueble, $claveEF_Cliente, $txtBusqueda);
    	return response()->json(['productos' => $buscarProductos], ApiStatus::OK);
    }

    public function buscarExistencias($claveEF_Empresa, $claveProducto)
    {
    	$buscarExistencias = Productos::buscarExistencias($claveEF_Empresa, $claveProducto);
    	return response()->json(['existencias' => $buscarExistencias], ApiStatus::OK);
    }

    public function sincronizarCatalogo(Request $request)
    {
        # Se genera la información a partir de la consulta        
        $catalogo = Productos::sincronizarCatalogo($request->claveEF_Inmueble);
        # Los datos obtenidos se convierten a una estructura de arreglos
        $datos = $this->object_to_array($catalogo);
        # Se genera el archivo CSV en la carpeta de storage
        Excel::create('aprosi_catalogo_sync_'.$request->claveEF_Inmueble, function($excel) use($datos)
        {
            $excel->sheet('Catalogo', function($sheet) use($datos)
            {
                $sheet->fromArray($datos);
            });
        })->store('csv', storage_path('excel/exports'));
        # Se obtiene el nombre automático del archivo y la ruta donde fue guardado
        $archivo = 'aprosi_catalogo_sync_'.$request->claveEF_Inmueble.'.csv';
        $path = storage_path('excel\exports').'\\'.$archivo;
        # Se sube el archivo al servidor FTP con las credenciales previamente configuradas
        $subida = Storage::disk('ftp')->putFileAs('/', new File($path), $archivo);        
        return $subida;
    }

    public function ejecutarSincronizacion()
    {
        # Se genera la información a partir de la consulta        
        $catalogo = Productos::sincronizarCatalogo(300000108);
        # Los datos obtenidos se convierten a una estructura de arreglos
        $datos = $this->object_to_array($catalogo);
        # Se genera el archivo CSV en la carpeta de storage
        Excel::create('aprosi_catalogo_sync_300000108', function($excel) use($datos)
        {
            $excel->sheet('Catalogo', function($sheet) use($datos)
            {
                $sheet->fromArray($datos);
            });
        })->store('csv', storage_path('excel/exports'));
        # Se obtiene el nombre automático del archivo y la ruta donde fue guardado      
        $archivo = 'aprosi_catalogo_sync_300000108.csv';
        $path = storage_path('excel/exports').'//'.$archivo;
        # Se sube el archivo al servidor FTP con las credenciales previamente configuradas
        $subida = Storage::disk('ftp')->putFileAs('/', new File($path), $archivo);        
        return $subida;
    }

    public function object_to_array($data) 
    {
        if(is_array($data) || is_object($data))
        {
            $result = array();
            foreach($data as $key => $value) 
            {
                $result[$key] = $this->object_to_array($value);
            }
            return $result;
        }
        return $data;
    }
}
