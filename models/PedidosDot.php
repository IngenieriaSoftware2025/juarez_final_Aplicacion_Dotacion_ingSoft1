<?php
namespace Model;

use Model\ActiveRecord;

class PedidosDot extends ActiveRecord {
    
    public static $tabla = 'jjjc_ped_dot';
    public static $columnasDB = [
        'ped_per_id',
        'ped_prenda_id',
        'ped_talla_id',
        'ped_cant_sol',
        'ped_observ',
        'ped_estado',
        'ped_situacion'
    ];

    public static $idTabla = 'ped_id';
    
    public $ped_id;
    public $ped_per_id;
    public $ped_prenda_id;
    public $ped_talla_id;
    public $ped_cant_sol;
    public $ped_fecha_sol;
    public $ped_observ;
    public $ped_estado;
    public $ped_situacion;
    
    public function __construct($args = [])
    {
        $this->ped_id = $args['ped_id'] ?? null;
        $this->ped_per_id = $args['ped_per_id'] ?? null;
        $this->ped_prenda_id = $args['ped_prenda_id'] ?? null;
        $this->ped_talla_id = $args['ped_talla_id'] ?? null;
        $this->ped_cant_sol = $args['ped_cant_sol'] ?? 0;
        $this->ped_fecha_sol = $args['ped_fecha_sol'] ?? '';
        $this->ped_observ = $args['ped_observ'] ?? '';
        $this->ped_estado = $args['ped_estado'] ?? 'PENDIENTE';
        $this->ped_situacion = $args['ped_situacion'] ?? 1;
    }
}