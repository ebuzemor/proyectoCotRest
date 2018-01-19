<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }           
            .email{
                margin:0 auto;                                    
                border:1px solid #ccc;
                border-radius:5px 5px 5px 5px;               
            }
            .img_2{                     
                height:65px;
                width:auto;                
            }
            .img_1{
                float:right;        
            }
            .email_head{
                background-color: rgb(250, 250, 250);
                height:65px;
                padding:0;                
                padding:5px;
            }
            .email_content{
                padding:8px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="email">
                        <div class="email_head">                                                    
                            <img src="http://www.aprosi.com.mx/wp-content/themes/Aprosi/images/logo_aprosi.png" class="img_2"  alt="Aprosi" />                             

                            <img src='http://www.copicocorp.com/DocumentosFiscales/image/logo.png' alt='copico' class="img_1" width='100' />  
                        </div>
                        <div class="email_content"> 
                            <h3>Saludos.</h3> 
                            <p>Nos es muy grato dirigirnos a Usted para hacerle llegar nuestros saludos y presentarles nuestra
                               Cotización de productos Aprosi, con las siguientes características:</p>
                        </div>                                                
                    </div>                      
                </div>
            </div>
        </div>           
    </body>
</html>
