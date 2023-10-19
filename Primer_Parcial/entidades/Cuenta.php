<?php

require_once './archivos/JsonFileManager.php';

class Cuenta{
    public $numeroDeCuenta;
    public $nombre;
    public $apellido;
    public $tipoDocumento;
    public $nroDocumento;
    public $email;
    public $tipoDeCuenta;
    public $moneda;
    public $saldo;
    private static $_archivo = null;
    public $imagen;

    private function __construct($nombre, $apellido, $tipoDocumento, $nroDocumento, $email, $tipoDeCuenta, $moneda, $saldo = 0) {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipoDocumento = $tipoDocumento;
        $this->nroDocumento = $nroDocumento;
        $this->email = $email;
        $this->tipoDeCuenta = $tipoDeCuenta;
        $this->moneda = $moneda;
        $this->saldo = $saldo;
        $this->numeroDeCuenta = self::generarNroDeCuenta();
    }

    private static function generarNroDeCuenta(){
        $datos = self::Json()->loadData();
        
        if($datos != false){
            $ultimoUsuario = end($datos);
            $ultimoNroCuenta = $ultimoUsuario->numeroDeCuenta;
            $ultimoNroCuenta++;
            return $ultimoNroCuenta;
        }else{
            return rand(100000, 999999);
        }
    }

    private static function Json() {
        if(self::$_archivo == null) {
            self::$_archivo = new JsonFileManager('./archivos/banco.json');
        }
        return self::$_archivo;
    }

    // 1 -------------------------------------------------------------------------------------------------------------------------------------
    public static function AltaUsuario($nombre, $apellido, $tipoDocumento, $nroDocumento, $email, $tipoDeCuenta, $moneda, $saldo = 0) {
        $retorno = false;
        $nuevoUsuario = new Cuenta($nombre, $apellido, strtoupper($tipoDocumento), $nroDocumento, $email, strtoupper($tipoDeCuenta), strtoupper($moneda), $saldo);
        if($nuevoUsuario->validarUsuario()) {
            $datos = self::Json()->loadData();
            $indice = $nuevoUsuario->buscarUsuario();
            if($indice !== null) {
                $datos[$indice]->saldo = $nuevoUsuario->saldo;
                self::Json()->saveData($datos);
                $retorno = true;
            }else{
                $nuevoUsuario->imagen = $nuevoUsuario->guardarImagen();
                if($nuevoUsuario->imagen !== null) {
                    self::Json()->appendData($nuevoUsuario);
                    $retorno = true;
                }
            }
        }else{
            echo "</br>Verificar si los datos estan vacios o si el tipo de cuenta (cc o ca) y moneda ($ o u\$s) es la correcta</br>";
        }
        return $retorno;
    }

    private function validarUsuario() {
        $retorno = false;
        if($this->nombre != '' && $this->apellido != '' && $this->nroDocumento != '' && $this->email != '' &&
          ($this->tipoDocumento == 'DNI' || $this->tipoDocumento == 'CI' || $this->tipoDocumento == 'PASAPORTE') && 
          ($this->tipoDeCuenta == 'CA' || $this->tipoDeCuenta == 'CC') && 
          ($this->moneda == '$' || $this->moneda == 'U$S')) {
            $retorno = true;
        }
        return $retorno;
    }

    private function buscarUsuario() {
        $retorno = null;
        $datos = self::Json()->loadData();
        if($datos != null) {
            foreach ($datos as $indice => $usuario) 
            {   // La validacion de busqueda esta mal redactada --- MODIFICACION EN UN FUTURO
                if($usuario->nombre == $this->nombre && $usuario->apellido == $this->apellido && $usuario->nroDocumento == $this->nroDocumento) {
                    $retorno = $indice;
                }
            }
        }
        return $retorno;
    }

    private function guardarImagen() {
        $retorno = null;
        if(isset($_FILES['imagen'])) {
            $retorno = null;
            $carpetaImg = './ImagenesDeCuentas/2023/';
            $nombreImg = $this->numeroDeCuenta . $this->tipoDeCuenta;
            $ruta = $carpetaImg . $nombreImg . ".jpg";
            if (!is_dir($carpetaImg)) {
                mkdir($carpetaImg, 777, true);
            }
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
                    $imagenGuardada = array(
                        'name' => $nombreImg,
                        'full_path' => $ruta,
                    );
                    $retorno = $imagenGuardada;
            }
        }else { echo "</br>Debe cargar una imagen para el registro</br>"; }
        return $retorno;
    }
    
    // 2 -------------------------------------------------------------------------------------------------------------------------------------
    public static function consultarCuenta($tipoDeCuenta, $nroDeCuenta) {
        $retorno = true;
        $indice = self::buscarCuenta($tipoDeCuenta, $nroDeCuenta);

        if($indice === true) {
            echo "</br>Tipo de cuenta incorrecto</br>";
        }else if($indice === false) {
            echo "</br>No existe el Nro de cuenta</br>";
            $retorno = false;
        }else{
            $datos = self::Json()->loadData();
            echo "Cuenta: ". $datos[$indice]->tipoDeCuenta."</br>";
            echo "Nro de cuenta: ".$datos[$indice]->numeroDeCuenta."</br>";
            echo "Moneda: ".$datos[$indice]->moneda."</br>";
            echo "Saldo: ".$datos[$indice]->saldo."</br>";
        }
        return $retorno;
    }

    private static function buscarCuenta($tipoDeCuenta, $nroDeCuenta) {
        $retorno = false;
        $datos = self::Json()->loadData();
        if($datos != null) {
            foreach ($datos as $indice => $usuario) {   
                if($usuario->numeroDeCuenta == $nroDeCuenta && $usuario->tipoDeCuenta == strtoupper($tipoDeCuenta)){
                    $retorno = $indice; 
                }elseif($usuario->numeroDeCuenta == $nroDeCuenta) {
                    $retorno = true;
                }
            }
        }
        return $retorno;
    }

    // 3 -------------------------------------------------------------------------------------------------------------------------------------
    public static function depositarImporte($tipoDeCuenta, $nroDeCuenta, $moneda, $importe) {
        $retorno = null;
        $datos = self::Json()->loadData();
        $indice = self::buscarCuenta($tipoDeCuenta, $nroDeCuenta);
        if(!is_bool($indice) && $datos[$indice]->moneda === strtoupper($moneda)) {
            $datos[$indice]->saldo += $importe;
            self::AltaUsuario($datos[$indice]->nombre, $datos[$indice]->apellido, $datos[$indice]->tipoDocumento, $datos[$indice]->nroDocumento, $datos[$indice]->email, $datos[$indice]->tipoDeCuenta, $datos[$indice]->moneda, $datos[$indice]->saldo);
            $retorno = $datos[$indice];
        }
        return $retorno;
    }

    // 5 -------------------------------------------------------------------------------------------------------------------------------------
    public static function modificarCuenta($nroDeCuenta, $nombre, $apellido, $tipoDocumento, $nroDocumento, $email, $tipoDeCuenta, $moneda) {
        $retorno = false;
        $cuentaAux = new Cuenta($nombre, $apellido, strtoupper($tipoDocumento), $nroDocumento, $email, strtoupper($tipoDeCuenta), strtoupper($moneda));
        if($cuentaAux->validarUsuario()) {
            $indice = self::buscarCuenta($tipoDeCuenta, $nroDeCuenta);
            $datos = self::Json()->loadData();
            if(!is_bool($indice) && $datos != null) {
                $datos[$indice]->nombre = $cuentaAux->nombre;
                $datos[$indice]->apellido = $cuentaAux->apellido;
                $datos[$indice]->tipoDocumento = $cuentaAux->tipoDocumento;
                $datos[$indice]->nroDocumento = $cuentaAux->nroDocumento;
                $datos[$indice]->email = $cuentaAux->email;
                $datos[$indice]->moneda = $cuentaAux->moneda;
                //$datos[$indice]->imagen = guardarImagen();
                self::Json()->saveData($datos);
                $retorno = true;
            }else{
                echo "No existe esa la cuenta nº ".$nroDeCuenta;
            }
            return $retorno;
        }
    }

    // 6 -------------------------------------------------------------------------------------------------------------------------------------
    public static function retirarImporte($tipoDeCuenta, $nroDeCuenta, $moneda, $importe) {
        $retorno = null;
        $datos = self::Json()->loadData();
        $indice = self::buscarCuenta($tipoDeCuenta, $nroDeCuenta);
        if(!is_bool($indice) && $datos[$indice]->moneda === strtoupper($moneda)) {
            if($datos[$indice]->saldo >= $importe) {
                $datos[$indice]->saldo -= $importe;
                self::AltaUsuario($datos[$indice]->nombre, $datos[$indice]->apellido, $datos[$indice]->tipoDocumento, $datos[$indice]->nroDocumento, $datos[$indice]->email, $datos[$indice]->tipoDeCuenta, $datos[$indice]->moneda, $datos[$indice]->saldo);
                $retorno = $datos[$indice];
            }else{
                echo "NO HAY SUFICIENTE DINERO DEPOSITADO EN LA CUENTA";
            }
        }else{
            echo "LA CUENTA NO EXISTE O LOS DATOS ESTAN MAL";
        }
        return $retorno;
    }

    // 7 -------------------------------------------------------------------------------------------------------------------------------------
    public static function traerCuenta($nroDeCuenta) {
        $retorno = null;
        $datos = self::Json()->loadData();
        if($datos != null) {
            foreach ($datos as $cuenta) {
                if($cuenta->numeroDeCuenta === $nroDeCuenta) {
                    $retorno = $cuenta;
                }
            }
        }
        return $retorno;
    }
}