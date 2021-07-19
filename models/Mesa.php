<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model{


    const ESTADO = array("Esperando", "Pagando", "Comiendo");
    protected $primaryKey = 'idMesas';
    protected $table = 'mesas';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'fechaCreacion';
    const UPDATED_AT = 'fechaModificacion';

    protected $fillable = [
        'cliente','codigo','estado'
    ];
}
?>