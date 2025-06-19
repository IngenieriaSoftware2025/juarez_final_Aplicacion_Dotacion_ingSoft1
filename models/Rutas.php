<?php

namespace Model;

use Model\ActiveRecord;

class Rutas extends ActiveRecord {
    
    public static $tabla = 'jjjc_rutas';
    public static $idTabla = 'ruta_id';
    public static $columnasDB = [
        'ruta_app_id',
        'ruta_nombre',
        'ruta_descripcion',
        'ruta_situacion'
    ];
    
    public $ruta_id;
    public $ruta_app_id;
    public $ruta_nombre;
    public $ruta_descripcion;
    public $ruta_situacion;
    
    public function __construct($datos = [])
    {
        $this->ruta_id = $datos['ruta_id'] ?? null;
        $this->ruta_app_id = $datos['ruta_app_id'] ?? '';
        $this->ruta_nombre = $datos['ruta_nombre'] ?? '';
        $this->ruta_descripcion = $datos['ruta_descripcion'] ?? '';
        $this->ruta_situacion = $datos['ruta_situacion'] ?? 1;
    }
}