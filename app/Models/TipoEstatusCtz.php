<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEstatusCtz extends Model
{
    protected $connection = 'copico';
    
    protected $table = 'tiposdestatusdecomprobantes';
}
