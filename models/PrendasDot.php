<?php
namespace Model;

use Model\ActiveRecord;

class PrendasDot extends ActiveRecord {
    
    public static $tabla = 'jjjc_dot_img';
    public static $columnasDB = [
        'prenda_nombre',
        'prenda_desc',
        'prenda_situacion'
    ];

    public static $idTabla = 'prenda_id';
    
    public $prenda_id;
    public $prenda_nombre;
    public $prenda_desc;
    public $prenda_fecha_crea;
    public $prenda_situacion;
    
    public function __construct($args = [])
    {
        $this->prenda_id = $args['prenda_id'] ?? null;
        $this->prenda_nombre = $args['prenda_nombre'] ?? '';
        $this->prenda_desc = $args['prenda_desc'] ?? '';
        $this->prenda_fecha_crea = $args['prenda_fecha_crea'] ?? '';
        $this->prenda_situacion = $args['prenda_situacion'] ?? 1;
    }
}