<?php

namespace Model;

// Modelo para la tabla asig_permisos
class AsigPermiso extends ActiveRecord {
    
    public static $tabla = 'jjjc_asig_permisos';
    public static $columnasDB = [
        'asignacion_usuario_id',
        'asignacion_app_id',
        'asignacion_permiso_id',
        //'asignacion_fecha',
        'asignacion_usuario_asigno',
        'asignacion_motivo',
        'asignacion_situacion'
    ];

    public static $idTabla = 'asignacion_id';
    
    public $asignacion_id;
    public $asignacion_usuario_id;
    public $asignacion_app_id;
    public $asignacion_permiso_id;
    public $asignacion_fecha;
    public $asignacion_usuario_asigno;
    public $asignacion_motivo;
    public $asignacion_situacion;

    public function __construct($args = []){
        $this->asignacion_id = $args['asignacion_id'] ?? null;
        $this->asignacion_usuario_id = $args['asignacion_usuario_id'] ?? '';
        $this->asignacion_app_id = $args['asignacion_app_id'] ?? '';
        $this->asignacion_permiso_id = $args['asignacion_permiso_id'] ?? '';
        $this->asignacion_fecha = $args['asignacion_fecha'] ?? date('Y-m-d');
        $this->asignacion_usuario_asigno = $args['asignacion_usuario_asigno'] ?? '';
        $this->asignacion_motivo = $args['asignacion_motivo'] ?? '';
        $this->asignacion_situacion = $args['asignacion_situacion'] ?? 1;
    }
}