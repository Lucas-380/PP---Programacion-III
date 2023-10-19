<?php
require_once './entidades/Cuenta.php';

function POST_cuentaAlta() 
{
    if (isset($_POST['nombre']) && isset($_POST['apellido']) && isset($_POST['tipoDocumento']) && isset($_POST['nroDocumento']) && 
        isset($_POST['email']) && isset($_POST['tipoDeCuenta']) && isset($_POST['moneda'])) 
        {
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $tipoDocumento = $_POST['tipoDocumento'];
            $nroDocumento = $_POST['nroDocumento'];
            $email = $_POST['email'];
            $tipoDeCuenta = $_POST['tipoDeCuenta'];
            $moneda = $_POST['moneda'];
            $saldoInicial = 0;

            if(isset($_POST['saldoInicial'])) {
                $saldoInicial = $_POST['saldoInicial'];
            }

            if($nombre != null && $apellido != null && $tipoDocumento != null && $nroDocumento != null && $email != null && $tipoDeCuenta != null && $moneda != null)
            {
                if(Cuenta::AltaUsuario($nombre, $apellido, $tipoDocumento, $nroDocumento, $email, $tipoDeCuenta, $moneda, $saldoInicial)) {
                    echo json_encode(['exito' => 'Usuario registrado correctamente']);
                }else{
                    echo json_encode(['error' => 'Usuario no se ha podido registrar']);
                }
            }
        }else{
            echo json_encode(['error' => 'Falta algun parametro']);
        }
}
