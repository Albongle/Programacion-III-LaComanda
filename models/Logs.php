<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logs extends Model
{

    protected $primaryKey = 'idLogs';
    protected $table = 'logs';
    public $incrementing = true;
    public $timestamps = true;
    const CREATED_AT = 'fechaCreacion';
    const UPDATED_AT = null;

    protected $fillable = [
        'idUsuario', 'horaRegistro','entidad','accion','detalle'
    ];

}


?>