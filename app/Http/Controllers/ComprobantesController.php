<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comprobantes;
use App\MyClass\ApiStatus;

class ComprobantesController extends Controller
{
    public function ConfirmarPago($codigoDeComprobante)
    {
    	$confirmado = Comprobantes::where('codigoDeComprobante', $codigoDeComprobante)->firstOrFail();
    	$confirmado->claveTipoDeStatusDeComprobante = 162;
    	$cambio = $confirmado->save();
    	if($cambio > 0)
    		return response()->json(["respuesta" => "El pago ha sido confirmado correctamente"], ApiStatus::OK);
    	else
    		return response()->json(["respuesta" => "El pago no se pudo confirmar"], ApiStatus::NO_CONTENT);
    }

    public function CancelarPedido($codigoDeComprobante)
    {
    	$cancelado = Comprobantes::where('codigoDeComprobante', $codigoDeComprobante)->firstOrFail();
    	$cancelado->claveTipoDeStatusDeComprobante = 163;
    	$cambio = $cancelado->save();
    	if($cambio > 0)
    		return response()->json(["respuesta" => "El pedido ha sido cancelado correctamente"], ApiStatus::OK);
    	else
    		return response()->json(["respuesta" => "El pedido no se pudo cancelar"], ApiStatus::NO_CONTENT);
    }

    public function InfoComprobante($claveComprobante)
    {
        $comprobante = Comprobantes::findOrFail($claveComprobante);//::where('claveComprobante', $claveComprobante)->firstOrFail();
        if($comprobante != null)
            return response()->json(["folioCodigoComprobante" => $comprobante->codigoDeComprobante], ApiStatus::OK);
        else
            return response()->json(["Error:" => " El comprobante con la clave solicitada no existe"], ApiStatus::NO_CONTENT);
    }
}
