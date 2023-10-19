<?php

require_once './archivos/JsonFileManager.php';

class Retiro implements JsonSerializable{
    private static $_archivo = null;
    public $id;
    public $fecha;
    public $monto;
    public $cuenta;

    private function __construct($cuenta, $monto) {
        $this->cuenta = $cuenta;
        $this->fecha = (new DateTime('now', new DateTimeZone('America/Argentina/Buenos_Aires')))->format('d-m-Y H:i:s');
        $this->monto = $monto;
        $this->id = self::generarId();
    }

    private static function generarId(){
        $datos = self::Json()->loadData();
        
        if($datos != false){
            $ultimoUsuario = end($datos);
            $ultimoNroCuenta = $ultimoUsuario->id;
            $ultimoNroCuenta++;
            return $ultimoNroCuenta;
        }else{
            return rand(100000, 999999);
        }
    }

    private static function Json() {
        if(self::$_archivo == null) {
            self::$_archivo = new JsonFileManager('./archivos/retiro.json');
        }
        return self::$_archivo;
    }
 
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'fecha' => $this->fecha,
            'monto' => $this->monto,
            'cuenta' => [
                'nombre' => $this->cuenta->nombre,
                'apellido' => $this->cuenta->apellido,
                'numeroDeCuenta' => $this->cuenta->numeroDeCuenta,
                'tipoDeCuenta' => $this->cuenta->tipoDeCuenta,
                'moneda' => $this->cuenta->moneda,
                'saldo' => $this->cuenta->saldo
            ]
        ];
    }

    public static function registrarRetiro($cuenta, $importe) {
        $retorno = false;
        $nuevoRetiro = new Retiro($cuenta, $importe);
        if(self::Json()->appendData($nuevoRetiro)) {
            $retorno = true;
        }         
        return $retorno;
    }

    public static function existeRetiro($nroRetiro) {
        $datos = self::Json()->loadData();
        $retorno = false;
        if($datos != null) {
            foreach ($datos as $retiro) {
                if($retiro->id == $nroRetiro) {
                    $retorno = true;
                }
            }
        }
        return $retorno;
    }

    public static function traerRetiro($nroRetiro) {
        $datos = self::Json()->loadData();
        $retorno = null;
        if($datos != null) {
            foreach ($datos as $retiro) {
                if($retiro->id == $nroRetiro) {
                    $retorno = $retiro;
                }
            }
        }
        return $retorno;
    }   

}