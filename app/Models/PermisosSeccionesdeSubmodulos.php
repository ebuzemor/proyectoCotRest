<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisosSeccionesdeSubmodulos extends Model
{
    protected $connection = 'copico';

    protected $table = 'permisos_seccionesdesubmodulos';

    public $timestamps = false;
}
