<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comprobantes extends Model
{
    protected $connection = 'copico';

    protected $table = 'comprobantes';

    protected $primaryKey = 'claveComprobante';

    public $timestamps = false;
}
