<?php
namespace Model;

use Model\ActiveRecord;

class TallasDot extends ActiveRecord {
    
    public static $tabla = 'jjjc_tallas_dot';
    public static $columnasDB = [
        'talla_nombre',
        'talla_desc',
        'talla_prenda_id',
        'talla_situacion'
    ];

    public static $idTabla = 'talla_id';
    
    public $talla_id;
    public $talla_nombre;
    public $talla_desc;
    public $talla_prenda_id;
    public $talla_situacion;
    
    public function __construct($args = [])
    {
        $this->talla_id = $args['talla_id'] ?? null;
        $this->talla_nombre = $args['talla_nombre'] ?? '';
        $this->talla_desc = $args['talla_desc'] ?? '';
        $this->talla_prenda_id = $args['talla_prenda_id'] ?? null;
        $this->talla_situacion = $args['talla_situacion'] ?? 1;
    }
}