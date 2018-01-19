<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', 'UsuariosController@login');
Route::group(['middleware' => 'auth:api'], function() {

	//USUARIOS
	Route::get('/buscarUsuario/{claveEF_Empresa}/{usuario}/{password}', 'UsuariosController@buscar');
	//CLIENTES
	Route::get('/buscarClientes/{claveEF_Empresa}/{txtBusqueda}', 'ClientesController@buscar');
	//PRODUCTOS
	Route::get('/buscarProductos/{claveEF_Inmueble}/{claveEF_Cliente}/{txtBusqueda}', 'ProductosController@buscarProductos');
	Route::get('/buscarExistencias/{claveEF_Empresa}/{claveProducto}', 'ProductosController@buscarExistencias');
	//SUCURSALES
	Route::get('/listaSucursales/{claveEF_Empresa}', 'InmueblesController@listaSucursales');
	Route::get('/obtenerSucursal/{claveEF_Inmueble}', 'InmueblesController@obtenerSucursal');
	//COTIZACIONES
	Route::get('/mostrarCondComCtz/{claveEF_Empresa}', 'CotizacionController@mostrarCondicionesComercialesCtz');
	Route::get('/estatusCotizacion', 'CotizacionController@obtenerTipoEstatusCtz');
	Route::post('/mostrarCotizaciones', 'CotizacionController@mostrarCotizaciones');
	Route::post('/guardarCotizacion','CotizacionController@guardarCotizacion');
	Route::post('/editarCotizacion','CotizacionController@editarCotizacion');
	Route::get('/cargarDetallesCotizacion/{claveComprobante}', 'CotizacionController@cargarDetallesCotizacion');
	//COTIZACIONES_MAIL
	Route::post('/enviarMail','CotizacionController@enviarMail');
	//COTIZACIONES_DESCARGAR_PDF
	Route::get('/descargarPDF/{claveEF_Empresa}/{claveComprobante}','CotizacionController@descargarPDF');
});