<!DOCTYPE html>
<html>
<head>
   <title>Cotización Aprosi</title>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   {{ Html::style('css/bootstrap.min.css') }}
   {{ Html::style('css/pdf.css') }}
</head>
<body>
    <div class="container-fluid">
        <div class="header">
            {{ Html::image('img/logotiposDeEmpresas/logo_aprosi.png', 'aprosi', array('class' => 'img_aprosi')) }}
            {{ Html::image('img/logotiposDeEmpresas/cpc.jpg', 'copico', array('class' => 'img_copico')) }}
            <h3><strong>APROSI EQUIPOS SA DE CV</strong></h3>    
            @foreach($comprobantes as $row)    
            <p style="font-size:10px"><b>{{utf8_decode($row->direccion)}}</b>  <b>{{$row->telefono}}</b></p>
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
                    <td colspan="2"><strong>CLIENTE: </strong></td><td colspan="2">{{$row->codigoDeCliente}} - {{$row->cliente}}</td>
                </tr>
                <tr>
                    <td><strong>FECHA DE VIGENCIA: </strong></td><td align="center">{{$row->fechaVigencia}}</td>
                    <td><strong>FECHA DE ENTREGA: </strong></td><td align="center">{{$fechaEntrega}}</td>
                </tr>
                @endforeach
            </table>                      
        </div>        
        <table class="table table-bordered table-condensed">   
            <tr class="table-secondary">
                <th align="center">CÓDIGO</th>
                <th align="center">DESCRIPCIÓN</th>                
                <th align="center">CANT</th>
                <th align="center">P. UNIT.</th>
                <th align="center">DESCUENTO</th>                
                <th align="center">IMPUESTOS</th>
                <th align="center">SUBTOTAL</th>
            </tr>
            @foreach($detallesComprobantes as $row)
            <tr>
                <td align="center">{{$row->codigoDeProducto}}</td>
                <td align="left">{{$row->descripcion}}</td>
                <td align="center">{{$row->cantidad}}</td>
                <td align="right">${{number_format($row->precioUnitario, 2)}}</td>
                <td align="right">${{number_format($row->importeDescuento, 2)}}</td>                
                <td align="right">${{number_format($row->impuestos, 2)}}</td>
                <td align="right">${{number_format($row->importe, 2)}}</td>
            </tr>
            @endforeach
        </table>
        <div class="row">
            <div class="col-md-4 offset-md-8">     
                <div class="totales">                  
                    <table class="table table-sm table-bordered table-condensed">                           
                        @foreach($comprobantes as $row)                                        
                        <tr><th class="table-secondary">SUBTOTAL: </th><td align="right">$ {{number_format($row->subtotal, 2)}}</td></tr>
                        <tr><th class="table-secondary">DESCUENTO: </th><td align="right">$ {{number_format($row->descuento, 2)}}</td></tr>
                        <tr><th class="table-secondary">SUBTOTAL CON DESCUENTO: </th><td align="right">$ {{number_format($row->subtotal - $row->descuento, 2)}}</td></tr>
                        <tr><th class="table-secondary">IVA: </th><td align="right">$ {{number_format($row->total_impuestos, 2)}}</td></tr>
                        <tr><th class="table-secondary">TOTAL: </th><td align="right">$ {{number_format($row->total, 2)}}</td></tr>
                        @endforeach
                    </table>   
                </div>
            </div>                    
        </div>        
        <div class="row">
            <div class="col-12">
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