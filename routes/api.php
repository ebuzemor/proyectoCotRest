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
Route::get('/ejecutarSincronizacion', 'ProductosController@ejecutarSincronizacion');
Route::group(['middleware' => 'auth:api'], function() {
	//USUARIOS
	Route::get('/buscarUsuario/{claveEF_Empresa}/{usuario}/{password}', 'UsuariosController@buscar');
	Route::get('/permisosUsuario/{claveEF_Empresa}/{claveEF_Usuario}', 'UsuariosController@permisos');
	Route::get('/cargarPermisos/{claveAplicacion}', 'UsuariosController@cargarPermisos');
	Route::get('/obtenerListaUsuarios/{txtBusqueda}', 'UsuariosController@obtenerUsuarios');
	Route::post('/guardarPermisos', 'UsuariosController@guardarPermisos');
	//CLIENTES
	Route::get('/buscarClientes/{claveEF_Empresa}/{txtBusqueda}', 'ClientesController@buscar');
	Route::post('/guardarCliente', 'ClientesController@guardarCliente');
	//SIXPLUS1 CONFIRMAR Y CANCELAR COTIZACIONES
	Route::get('/confirmarPago/{codigoDeComprobante}', 'ComprobantesController@ConfirmarPago');
	Route::get('/cancelarPedido/{codigoDeComprobante}', 'ComprobantesController@CancelarPedido');
	//PRODUCTOS
	Route::get('/buscarProductos/{claveEF_Inmueble}/{claveEF_Cliente}/{txtBusqueda}', 'ProductosController@buscarProductos');
	Route::get('/buscarExistencias/{claveEF_Empresa}/{claveProducto}', 'ProductosController@buscarExistencias');
	Route::post('/sincronizarCatalogo', 'ProductosController@sincronizarCatalogo');
	//FICHA_TECNICA
	Route::get('/buscarFichaTecnica/{claveProducto}', 'FichaTecnicaController@obtenerFichaTecnica');
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
	//REPORTES
	Route::post('/listaCotizacionesUsr', 'ReportesController@listaCotizaciones');
	Route::post('/reporteCotizacionesUsr', 'ReportesController@reporteCotizaciones');
});