<?php

require_once './archivos/JsonFileManager.php';

class Ajuste implements JsonSerializable{
    private static $_archivo = null;
    public $id;
    public $nroDeOperacion;
    public $operacion;
    public $motivo;
    public $monto;

    private function __construct($nroDeOperacion, $operacion, $motivo, $monto) {
        $this->id = self::generarId();
        $this->nroDeOperacion = $nroDeOperacion;
        $this->operacion = $operacion;
        $this->motivo = $motivo;
        $this->monto = $monto;
    }

    private static function generarId(){
        $datos = self::Json()->loadData();
        
        if($datos != false){
            $ultimoAjuste = end($datos);
            $ultimoNroAjuste = $ultimoAjuste->id_ajuste;
            $ultimoNroAjuste++;
            return $ultimoNroAjuste;
        }else{
            return rand(100000, 999999);
        }
    }

    private static function Json() {
        if(self::$_archivo == null) {
            self::$_archivo = new JsonFileManager('./archivos/ajuste.json');
        }
        return self::$_archivo;
    }

    public function jsonSerialize() {
        return [
            'id_ajuste' => $this->id,
            'nroDeOperacion' => $this->nroDeOperacion,
            'operacion' => $this->operacion,
            'motivo' => $this->motivo,
            'monto' => $this->monto
        ];
    }

    public static function registrarAjuste($nroDeOperacion, $operacion, $motivo, $monto) {
        $retorno = false;
        $nuevoAjuste = new Ajuste($nroDeOperacion, $operacion, $motivo, $monto);

        if($nuevoAjuste->validarAjuste() && self::Json()->appendData($nuevoAjuste)) {
            $retorno = true;
        }         
        return $retorno;
    }

    private function validarAjuste() {
        $retorno = false;
        if(($this->operacion === 'DEPOSITO' || $this->operacion === 'EXTRACCION') && $this->motivo != null && is_numeric($this->monto)) {
            $retorno = true;
        }
        return $retorno;
    }

    public static function validarMontoAjuste($operacion, $monto) {
        $retorno = false;
        if($operacion != null && $monto != null) { 
            $resultado = $operacion->monto + $monto;
            if($resultado > -1) {
                $retorno = true;
            }
        }
        return $retorno;
    }



}