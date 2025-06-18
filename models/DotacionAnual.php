<?php

namespace Model;

use Model\ActiveRecord;

class DotacionAnual extends ActiveRecord {
    
    public static $tabla = 'jjjc_dot_anual';
    public static $columnasDB = [
        'dot_anual_per_id',
        'dot_anual_anio',
        'dot_anual_cant_ent',
        'dot_anual_fecha_ult_ent',
        'dot_anual_situacion'
    ];

    public static $idTabla = 'dot_anual_id';
    
    public $dot_anual_id;
    public $dot_anual_per_id;
    public $dot_anual_anio;
    public $dot_anual_cant_ent;
    public $dot_anual_fecha_ult_ent;
    public $dot_anual_situacion;
    
    public function __construct($args = [])
    {
        $this->dot_anual_id = $args['dot_anual_id'] ?? null;
        $this->dot_anual_per_id = $args['dot_anual_per_id'] ?? null;
        $this->dot_anual_anio = $args['dot_anual_anio'] ?? date('Y');
        $this->dot_anual_cant_ent = $args['dot_anual_cant_ent'] ?? 0;
        $this->dot_anual_fecha_ult_ent = $args['dot_anual_fecha_ult_ent'] ?? '';
        $this->dot_anual_situacion = $args['dot_anual_situacion'] ?? 1;
    }
}