<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntidadesFiscales extends Model
{
    protected $connection = 'copico';

    protected $table = 'entidadesfiscales';

    protected $primaryKey = 'claveEntidadFiscal';

    public $incrementing = false;
}
