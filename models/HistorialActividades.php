<?php

namespace Model;

use Model\ActiveRecord;

class HistorialActividades extends ActiveRecord {
    
    public static $tabla = 'jjjc_historial_act';
    public static $idTabla = 'historial_id';
    public static $columnasDB = [
        'historial_usuario_id',
        'historial_fecha',
        'historial_ruta',
        'historial_ejecucion',
        'historial_status',
        'historial_situacion'
    ];
    
    public $historial_id;
    public $historial_usuario_id;
    public $historial_fecha;
    public $historial_ruta;
    public $historial_ejecucion;
    public $historial_status;
    public $historial_situacion;
    
    public function __construct($datos = [])
    {
        $this->historial_id = $datos['historial_id'] ?? null;
        $this->historial_usuario_id = $datos['historial_usuario_id'] ?? '';
        $this->historial_fecha = $datos['historial_fecha'] ?? '';
        $this->historial_ruta = $datos['historial_ruta'] ?? '';
        $this->historial_ejecucion = $datos['historial_ejecucion'] ?? '';
        $this->historial_status = $datos['historial_status'] ?? 1;
        $this->historial_situacion = $datos['historial_situacion'] ?? 1;
    }
}