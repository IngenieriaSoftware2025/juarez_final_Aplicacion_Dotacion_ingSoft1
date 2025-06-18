<?php
namespace Model;

use Model\ActiveRecord;

class InventarioDot extends ActiveRecord {
    
    public static $tabla = 'jjjc_inv_dot';
    public static $columnasDB = [
        'inv_prenda_id',
        'inv_talla_id',
        'inv_cant_disp',
        'inv_cant_total',
        'inv_lote',
        'inv_observ',
        'inv_situacion'
    ];

    public static $idTabla = 'inv_id';
    
    public $inv_id;
    public $inv_prenda_id;
    public $inv_talla_id;
    public $inv_cant_disp;
    public $inv_cant_total;
    public $inv_fecha_ing;
    public $inv_lote;
    public $inv_observ;
    public $inv_situacion;
    
    public function __construct($args = [])
    {
        $this->inv_id = $args['inv_id'] ?? null;
        $this->inv_prenda_id = $args['inv_prenda_id'] ?? null;
        $this->inv_talla_id = $args['inv_talla_id'] ?? null;
        $this->inv_cant_disp = $args['inv_cant_disp'] ?? 0;
        $this->inv_cant_total = $args['inv_cant_total'] ?? 0;
        $this->inv_fecha_ing = $args['inv_fecha_ing'] ?? '';
        $this->inv_lote = $args['inv_lote'] ?? '';
        $this->inv_observ = $args['inv_observ'] ?? '';
        $this->inv_situacion = $args['inv_situacion'] ?? 1;
    }
}