<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\File;
use App\Models\Productos;
use Excel;
use Storage;

class SincronizacionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronExecSync:Sincronizacion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta una consulta, genera un archivo csv y lo sube por FTP.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
