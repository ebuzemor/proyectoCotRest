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
    	return response()->json(['fichaTecnica' => $fichaTecnica], ApiStatus::OK);
    }
}
