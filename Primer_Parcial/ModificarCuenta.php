<?php
require_once './entidades/Cuenta.php';

function PUT_modificarCuenta(){
    parse_str(file_get_contents("php://input"), $putData);
    // $nombre, $apellido, $tipoDocumento, $nroDocumento, $email, $tipoDeCuenta, $moneda, $saldo = 0
    if (isset($putData['nroDeCuenta'], $putData['nombre'], $putData['apellido'], $putData['tipoDocumento'], $putData['nroDocumento'], $putData['email'], $putData['tipoDeCuenta'], $putData['moneda'])) 
    {
        $nroDeCuenta = $putData['nroDeCuenta'];
        $nombre = $putData['nombre'];
        $apellido = $putData['apellido'];
        $tipoDocumento = $putData['tipoDocumento'];
        $nroDocumento = $putData['nroDocumento'];
        $email = $putData['email'];
        $tipoDeCuenta = $putData['tipoDeCuenta'];
        $moneda = $putData['moneda'];
        if(Cuenta::modificarCuenta($nroDeCuenta, $nombre, $apellido, $tipoDocumento, $nroDocumento, $email, $tipoDeCuenta, $moneda)) {
            echo json_encode(['exito' => 'Cuenta modificada correctamente']);
        }else{
            echo json_encode(['error' => 'No se pudo modificar la cuenta - verifique si los parametros son correctos']);
        }
        
    } else {
        echo json_encode(['error' => 'Falta algun parametro']);
    }
}