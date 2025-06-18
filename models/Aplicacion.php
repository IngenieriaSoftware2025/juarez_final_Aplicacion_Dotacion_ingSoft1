<?php

namespace Model;


class Aplicacion extends ActiveRecord {
    
    public static $tabla = 'jjjc_aplicacion';
    public static $columnasDB = [
        'app_nombre_largo',
        'app_nombre_medium',
        'app_nombre_corto',
        //'app_fecha_creacion',
        'app_situacion'
    ];

    public static $idTabla = 'app_id';
    
    public $app_id;
    public $app_nombre_largo;
    public $app_nombre_medium;
    public $app_nombre_corto;
    public $app_fecha_creacion;
    public $app_situacion;

    public function __construct($args = []){
        $this->app_id = $args['app_id'] ?? null;
        $this->app_nombre_largo = $args['app_nombre_largo'] ?? '';
        $this->app_nombre_medium = $args['app_nombre_medium'] ?? '';
        $this->app_nombre_corto = $args['app_nombre_corto'] ?? '';
        $this->app_fecha_creacion = $args['app_fecha_creacion'] ?? date('Y-m-d');
        $this->app_situacion = $args['app_situacion'] ?? 1;
    }
}