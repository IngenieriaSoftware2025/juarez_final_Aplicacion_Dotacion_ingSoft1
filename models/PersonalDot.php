<?php
namespace Model;

use Model\ActiveRecord;

class PersonalDot extends ActiveRecord {
    
    public static $tabla = 'jjjc_personal_dot';
    public static $columnasDB = [
        'per_nom1',
        'per_nom2',
        'per_ape1',
        'per_ape2',
        'per_dpi',
        'per_tel',
        'per_direc',
        'per_correo',
        'per_puesto',
        'per_area',
        'per_situacion'
    ];

    public static $idTabla = 'per_id';
    
    public $per_id;
    public $per_nom1;
    public $per_nom2;
    public $per_ape1;
    public $per_ape2;
    public $per_dpi;
    public $per_tel;
    public $per_direc;
    public $per_correo;
    public $per_puesto;
    public $per_area;
    public $per_fecha_ing;
    public $per_situacion;
    
    public function __construct($args = [])
    {
        $this->per_id = $args['per_id'] ?? null;
        $this->per_nom1 = $args['per_nom1'] ?? '';
        $this->per_nom2 = $args['per_nom2'] ?? '';
        $this->per_ape1 = $args['per_ape1'] ?? '';
        $this->per_ape2 = $args['per_ape2'] ?? '';
        $this->per_dpi = $args['per_dpi'] ?? '';
        $this->per_tel = $args['per_tel'] ?? 0;
        $this->per_direc = $args['per_direc'] ?? '';
        $this->per_correo = $args['per_correo'] ?? '';
        $this->per_puesto = $args['per_puesto'] ?? '';
        $this->per_area = $args['per_area'] ?? '';
        $this->per_fecha_ing = $args['per_fecha_ing'] ?? '';
        $this->per_situacion = $args['per_situacion'] ?? 1;
    }
}