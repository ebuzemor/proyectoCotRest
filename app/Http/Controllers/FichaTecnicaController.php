<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FichaTecnica;
use App\MyClass\ApiStatus;

class FichaTecnicaController extends Controller
{
    public function obtenerFichaTecnica($claveProducto)
    {
    	$fichaTecnica = FichaTecnica::buscarFichaTecnica($claveProducto);
    	if($fichaTecnica != null)
    		return response()->json(['fichaTecnica' => $fichaTecnica], ApiStatus::OK);
    	else
    		return response()->json(['fichaTecnica' => 'Sin datos'], ApiStatus::NO_CONTENT);
    }
}
