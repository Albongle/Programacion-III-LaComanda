<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facturacion extends Model{


    protected $primaryKey = 'idFacturacion';
    protected $table = 'facturacion';
    public $incrementing = true;
    public $timestamps = true;
    const CREATED_AT = 'fechaCreacion';
    const UPDATED_AT = null;


    protected $fillable = [
        'idMesa','importe'
    ];

}




?>