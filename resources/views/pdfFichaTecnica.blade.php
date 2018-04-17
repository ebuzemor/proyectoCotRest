<!DOCTYPE html>
<html>
<head>
	<title>Cotización Aprosi</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/pdf.css">
</head>
<body>
<div class="container-fluid">
    <div class="header">
        <img src="img/logotiposDeEmpresas/logo_aprosi.png" class="img_aprosi" />
        <img src="img/logotiposDeEmpresas/cpc.jpg" class="img_copico" />
         <h3><strong>APROSI EQUIPOS SA DE CV</strong></h3>
         @foreach($comprobantes as $row)
            <p style="font-size:10px"><b>{{utf8_decode($row->direccion)}}</b>  <b> {{$row->telefono}}</b></p>
         @endforeach
         <p><strong>RFC:</strong> AEQ891204TH3</p>
    </div>
    <div class="subheader">
        <table class="table table-bordered table-condensed">
            @foreach($comprobantes as $row)
            <tr>
                <td colspan="2"><strong>FECHA EMISIÓN: </strong></td><td colspan="2">{{$row->fechaEmision}}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>CÓDIGO DE COTIZACIÓN: </strong></td><td colspan="2">{{$row->codigoDeComprobante}}</td>
            </tr>
            <tr>
                <td colspan="2"  ><strong>CLIENTE: </strong></td><td colspan="2">{{$row->codigoDeCliente}} - {{$row->cliente}}</td>
            </tr>
            <tr colspan="2">
                <td><strong>FECHA DE VIGENCIA: </strong></td><td>{{$row->fechaVigencia}}</td>                
                <td><strong>FECHA DE ENTREGA: </strong></td><td>{{$fechaEntrega}}</td>                
            </tr>
            @endforeach            
        </table>                      
    </div>
    
        <table class="table table-bordered table-condensed">   
            <tr class="table-secondary">                
                <th>IMAGEN</th>                
                <th>CÓDIGO</th>
                <th>DESCRIPCIÓN</th>                
                <th>CANT</th>
                <th>DIAS ENTREGA</th>   
                <th>P. UNIT.</th>
                <th>DESCUENTO</th>                                
                <th>SUBTOTAL</th>
            </tr>           

            @foreach($detallesComprobantes as $row)      
            <tr>
                <td class="text-center">                    
                @php
                    if($row->contenido)
                    {
                        echo '<img src="data:image/'.$row->extension.';base64,'.base64_encode($row->contenido).'" style="padding-top:10px" alt="" />';
                    }                                                                                                                                                   
                @endphp                                              
                </td>                               
                <td class="text-center">{{$row->codigoDeProducto}}</td>
                <td class="text-center">{{$row->descripcion}}</td>
                <td class="text-center" colspan="1" >{{$row->cantidad}}</td>
                <td class="text-center" colspan="1" >{{$row->diasDeEntrega}}</td>
                <td class="text-right" colspan="1" >${{number_format($row->precioUnitario, 2)}}</td>
                <td class="text-right" colspan="1">${{number_format($row->importeDescuento, 2)}}</td>                                
                <td class="text-right" colspan="1">${{number_format($row->cantidad*$row->precioUnitario-$row->importeDescuento, 2)}}</td>
            </tr> 
            <tr>
                <td class="text-left text-dark" colspan="3">                    
                    @if($row->detalles)                        
                        <strong>DESCRIPCIÓN: </strong><em>{{$row->detalles}}</em>                                  
                    @endif
                </td>
                <td colspan="5"></td>
            </tr>
            @endforeach
        </table>  
      
        <div class="row">
            <div class="col-md-12">     
                <div class="totales">                  
                    <table class="table table-sm table-condensed">                                                   
                        @foreach($comprobantes as $row)                                        
                        <tr><td style="width:600px;border:1px white;"></td><th class="table-secondary text-right">SUBTOTAL: </th><td style="border:1px solid #DEE2E6;" class="text-right">$ {{number_format($row->subtotal, 2)}}</td></tr>
                        <tr><td style="width:600px;border:1px white;"></td><th class="table-secondary text-right">DESCUENTO: </th><td style="border:1px solid #DEE2E6;" class="text-right">$ {{number_format($row->descuento, 2)}}</td></tr>                        
                        <tr><td  style="width:600px;border:1px white;"></td><th class="table-secondary text-right">IVA: </th><td style="border:1px solid #DEE2E6;" class="text-right">$ {{number_format($row->total_impuestos, 2)}}</td></tr>
                        <tr><td  style="width:600px;border:1px white;"></td><th class="table-secondary text-right">TOTAL: </th><td style="border:1px solid #DEE2E6;" class="text-right">$ {{number_format($row->total, 2)}}</td></tr>                                                                                                                    
                        @endforeach
                    </table>   
                </div>
            </div>                       
        </div>        
        <div class="row">
            <div class="col-md-12">
                <div class="condComerciales">   
                    <p><strong>CONDICIONES COMERCIALES:</strong></p>                            
                    @for ($i = 0; $i < count($condComercial);  $i++)
                        <p>{{$condComercial[$i]}}</p>                                           
                    @endfor                                
                </div>
            </div>
        </div>            

            <div class="observ">
                <p><strong>OBSERVACIONES</strong></p>            
                @for ($i = 0; $i < count($observaciones);  $i++)
                        <div class="sub_observ" >{{$observaciones[$i]}}</div>                                           
                @endfor       
            </div>       
    <div class="footer">
        <p class="text-center">APROSI EQUIPOS SA DE CV</p>     
        <p class="text-center">www.aprosi.com.mx</p>   
    </div>
</div>
</body>
</html>