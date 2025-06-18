<?php

namespace Model;

use Model\ActiveRecord;

class EntregasDot extends ActiveRecord {
    
    public static $tabla = 'jjjc_ent_dot';
    public static $columnasDB = [
        'ent_per_id',
        'ent_ped_id',
        'ent_inv_id',
        'ent_cant_ent',
        'ent_usuario_ent',
        'ent_observ',
        'ent_situacion'
    ];

    public static $idTabla = 'ent_id';
    
    public $ent_id;
    public $ent_per_id;
    public $ent_ped_id;
    public $ent_inv_id;
    public $ent_cant_ent;
    public $ent_fecha_ent;
    public $ent_usuario_ent;
    public $ent_observ;
    public $ent_situacion;
    
    public function __construct($args = [])
    {
        $this->ent_id = $args['ent_id'] ?? null;
        $this->ent_per_id = $args['ent_per_id'] ?? null;
        $this->ent_ped_id = $args['ent_ped_id'] ?? null;
        $this->ent_inv_id = $args['ent_inv_id'] ?? null;
        $this->ent_cant_ent = $args['ent_cant_ent'] ?? 0;
        $this->ent_fecha_ent = $args['ent_fecha_ent'] ?? '';
        $this->ent_usuario_ent = $args['ent_usuario_ent'] ?? null;
        $this->ent_observ = $args['ent_observ'] ?? '';
        $this->ent_situacion = $args['ent_situacion'] ?? 1;
    }
}