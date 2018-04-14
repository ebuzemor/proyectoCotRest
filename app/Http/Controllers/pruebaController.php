<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class pruebaController extends Controller
{
    public function imagenes()
    {
    	$archivos = array_slice(scandir('C:/ImgsAprosi'), 2);
    	$archivo = file_get_contents('C:/ImgsAprosi/'.$archivos[0]);
    	return base64_encode($archivo);
    }
}
