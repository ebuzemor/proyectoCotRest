<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class condicionesComercialesCtz extends Model
{
    protected $connection = 'copico';
    
    protected $table = 'condicionescomercialesdecotizaciones';
}
