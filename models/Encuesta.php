<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encuesta extends Model{


    protected $primaryKey = 'idEncuestas';
    protected $table = 'encuestas';
    public $incrementing = true;
    public $timestamps = true;
    const CREATED_AT = 'fechaCreacion';
    const UPDATED_AT = null;


    protected $fillable = [
        'dni','nombreCliente','idMesa','idMozo','idCocinero','descripcion','valMesa','valMozo','valCocinero',
        'valRestaurante'
    ];

}




?>