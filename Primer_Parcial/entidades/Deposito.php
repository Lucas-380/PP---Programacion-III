<?php

require_once './archivos/JsonFileManager.php';

class Deposito implements JsonSerializable{
    private static $_archivo = null;
    public $id;
    public $fecha;
    public $monto;
    public $cuenta;
    public $imagen;

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
            self::$_archivo = new JsonFileManager('./archivos/depositos.json');
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
            ],
            'imagen' => $this->imagen
        ];
    }

    public static function registrarDeposito($cuenta, $importe) {
        $retorno = false;
        $nuevoDeposito = new Deposito($cuenta, $importe);
        $nuevoDeposito->imagen = $nuevoDeposito->guardarImagen();
        if($nuevoDeposito->imagen !== null && self::Json()->appendData($nuevoDeposito)) {
            $retorno = true;
        }         
        return $retorno;
    }

    private function guardarImagen() {
        $retorno = null;
        if(isset($_FILES['imagen'])) {
            $retorno = null;
            $carpetaImg = './ImagenesDeDepositos2023/';
            $nombreImg = $this->cuenta->tipoDeCuenta . $this->cuenta->numeroDeCuenta . "_" . $this->id;
            $ruta = $carpetaImg . $nombreImg . ".jpg";
            if (!is_dir($carpetaImg)) {
                mkdir($carpetaImg, 777, true);
            }
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)){
                    $imagenGuardada = array(
                        'name' => $nombreImg,
                        'full_path' => $ruta,
                    );
                    $retorno = $imagenGuardada;
            }
        }else {
            echo "</br>Debe cargar una imagen para el registro del deposito</br>";
        }
        return $retorno;
    }

    // 4a ---------------------------------------------------------------------------------------------------------------------------------------------
    public static function consultarDepositos($fecha = null) {
        if($fecha == null) {
            $ayer = new DateTime('now', new DateTimeZone('America/Argentina/Buenos_Aires'));
            $ayer->modify('-1 day');
            $depositosEnFecha = self::traerDepositosEnFecha($ayer->format('d-m-Y'));
        }else{
            $depositosEnFecha = self::traerDepositosEnFecha($fecha);
        }
        return $depositosEnFecha;
    }

    private static function traerDepositosEnFecha($fecha) {
        $datos = self::Json()->loadData();
        $depositosEnFecha = [];
    
        if($datos != null) {
            foreach ($datos as $deposito) {
                $partes = explode(' ', $deposito->fecha);
                if($partes[0] == $fecha) {
                    array_push($depositosEnFecha, $deposito);
                }
            }
        }
        return $depositosEnFecha;
    }

    public static function mostrarTotalDeMontos($depositos) {
        if($depositos != []) {
            $montoCCpesos = 0;
            $montoCApesos = 0;
            $montoCCdolares = 0;
            $montoCAdolares = 0;

            foreach ($depositos as $deposito) {

                switch ($deposito->cuenta->tipoDeCuenta) {
                    case 'CC':
                        if($deposito->cuenta->moneda == "$") {
                            $montoCCpesos += $deposito->monto;
                        }else{
                            $montoCCdolares += $deposito->monto;
                        }
                        break;
                    case 'CA':
                        if($deposito->cuenta->moneda == "$") {
                            $montoCApesos += $deposito->monto;
                        }else{
                            $montoCAdolares += $deposito->monto;
                        }
                        break;
                }
            }
            $fecha = explode(' ', $deposito->fecha);
            echo "</br>Totales montos de depositos de la fecha: ". $fecha[0];
            echo "</br>monto CC Pesos: " . $montoCCpesos;
            echo "</br>monto CA Pesos: " . $montoCApesos;
            echo "</br>monto CC Dolares: " . $montoCCdolares;
            echo "</br>monto CA Dolares: " . $montoCAdolares;
        }
    }

    // 4b ---------------------------------------------------------------------------------------------------------------------------------------------
    public static function traerDepositosDeUsuario($usuario) {
        $datos = self::Json()->loadData();
        $depositosEnFecha = [];
    
        if($datos != null) {
            foreach ($datos as $deposito) {
                if($usuario == $deposito->cuenta->numeroDeCuenta) {
                    array_push($depositosEnFecha, $deposito);
                }
            }
        }
        return $depositosEnFecha;
    }

    private static function mostrarDeposito($deposito) {
        if($deposito != null) {
            echo "</br>-------DEPOSITO-------";
            echo "</br>id de deposito: ". $deposito->id;
            echo "</br>fecha: " . $deposito->fecha;
            echo "</br>monto: " . $deposito->monto;
            echo "</br>----------cuenta-----------</br>numero de cuenta: " . $deposito->cuenta->numeroDeCuenta;
            echo "</br>tipo: " . $deposito->cuenta->nombre ." ". $deposito->cuenta->apellido;
            echo "</br>tipo: " . $deposito->cuenta->tipoDeCuenta;
            echo "</br>moneda: " . $deposito->cuenta->moneda;
            echo "</br>saldo: " . $deposito->cuenta->saldo;
            echo "</br>imagen: <img width=100 src=".$deposito->imagen->full_path.">";
            echo "</br>-----------------------------";
        }
    }

    public static function listarDepositos($depositos) {
        foreach ($depositos as $deposito) {
            Deposito::mostrarDeposito($deposito);
        }
    }

    // 4c ---------------------------------------------------------------------------------------------------------------------------------------------
    public static function traerVentasEntreFechas($fechaUno, $fechaDos) {
        $depositos = self::Json()->loadData();
        $resultado = array_filter($depositos, function($objeto) use ($fechaUno, $fechaDos) {
            return self::filtrarPorRangoDeFechas($objeto, $fechaUno, $fechaDos);
        });
        return $resultado;
    }
    
    private static function filtrarPorRangoDeFechas($objeto, $fechaInicio, $fechaFin) {
        $fechaObjeto = strtotime($objeto->fecha);
        $fechaInicio = strtotime($fechaInicio);
        $fechaFin = strtotime($fechaFin);
        return ($fechaObjeto >= $fechaInicio && $fechaObjeto <= $fechaFin);
    }
    
    public static function ordenarPorNombre($arrayDepositos) {
        usort($arrayDepositos, function($a, $b) {
            return strcmp($a->cuenta->nombre, $b->cuenta->nombre);
        });
        return $arrayDepositos;
    }
    
    // 4d ---------------------------------------------------------------------------------------------------------------------------------------------
    public static function traerDepositosPorTipoDeCuenta($tipoDeCuenta) {
        $datos = self::Json()->loadData();
        $depositosEnFecha = [];
        if($datos != null) {
            foreach ($datos as $deposito) {
                if($tipoDeCuenta == $deposito->cuenta->tipoDeCuenta) {
                    array_push($depositosEnFecha, $deposito);
                }
            }
        }
        return $depositosEnFecha;
    }

    // 4e ---------------------------------------------------------------------------------------------------------------------------------------------
    public static function traerDepositosPorMoneda($moneda) {
        $datos = self::Json()->loadData();
        $depositosEnFecha = [];
        if($datos != null) {
            foreach ($datos as $deposito) {
                if($moneda == $deposito->cuenta->moneda) {
                    array_push($depositosEnFecha, $deposito);
                }
            }
        }
        return $depositosEnFecha;
    }

    // 7 ----------------------------------------------------------------------------------------------------------------------------------------------
    public static function existeDeposito($nroDeposito) {
        $datos = self::Json()->loadData();
        $retorno = false;
        if($datos != null) {
            foreach ($datos as $deposito) {
                if($deposito->id == $nroDeposito) {
                    $retorno = true;
                }
            }
        }
        return $retorno;
    }   
    
    
    public static function traerDeposito($nroDeposito) {
        $datos = self::Json()->loadData();
        $retorno = null;
        if($datos != null) {
            foreach ($datos as $deposito) {
                if($deposito->id == $nroDeposito) {
                    $retorno = $deposito;
                }
            }
        }
        return $retorno;
    }   
}