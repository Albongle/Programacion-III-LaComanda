<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{

    const TIPO = array("Empleado","Socio","Admin");
    const FUNCION = array("Bartender","Mozo","Cocinero");
    const SECTOR = array("Tragos","Choperas","Candy Bar","Cocina");
    const ESTADO = array("Activo","Suspendido","Baja");
    use SoftDeletes;

    protected $primaryKey = 'idUsuarios';
    protected $table = 'usuarios';
    public $incrementing = true;
    public $timestamps = true;
    const CREATED_AT = 'fechaCreacion';
    const UPDATED_AT = 'fechaModificacion';
    const DELETED_AT = 'fechaBaja';

    protected $fillable = [
        'clave','funcion','sector', 'nombre', 'apellido', 'mail', 'estado', 'tipo'
    ];

}


?>