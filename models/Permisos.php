<?php

namespace Model;

class Permisos extends ActiveRecord {
    
    public static $tabla = 'jjjc_permiso';
    public static $columnasDB = [
        'permiso_app_id',
        'permiso_nombre',
        'permiso_clave',
        'permiso_desc',
        //'permiso_fecha',
        'permiso_situacion'
    ];

    public static $idTabla = 'permiso_id';
    
    public $permiso_id;
    public $permiso_app_id;
    public $permiso_nombre;
    public $permiso_clave;
    public $permiso_desc;
    public $permiso_fecha;
    public $permiso_situacion;

    public function __construct($args = []){
        $this->permiso_id = $args['permiso_id'] ?? null;
        $this->permiso_app_id = $args['permiso_app_id'] ?? '';
        $this->permiso_nombre = $args['permiso_nombre'] ?? '';
        $this->permiso_clave = $args['permiso_clave'] ?? '';
        $this->permiso_desc = $args['permiso_desc'] ?? '';
        $this->permiso_fecha = $args['permiso_fecha'] ?? date('Y-m-d');
        $this->permiso_situacion = $args['permiso_situacion'] ?? 1;
    }
}