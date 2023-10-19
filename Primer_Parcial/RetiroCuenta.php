<?php
require_once './entidades/Cuenta.php';
require_once './entidades/Retiro.php';

function POST_retiroCuenta() 
{
    if (isset($_POST['tipoDeCuenta']) && isset($_POST['nroDeCuenta']) && isset($_POST['moneda']) && isset($_POST['importe'])) 
    {
        $tipoDeCuenta = $_POST['tipoDeCuenta'];
        $nroDeCuenta = $_POST['nroDeCuenta'];
        $moneda = $_POST['moneda'];
        $importe = $_POST['importe'];

        if($tipoDeCuenta != null && $nroDeCuenta != null && $moneda != null && $importe != null)
        {
            $cuentaActualizada = Cuenta::retirarImporte($tipoDeCuenta, $nroDeCuenta, $moneda, $importe);
            if($cuentaActualizada != null && Retiro::registrarRetiro($cuentaActualizada, $importe)) {
                echo json_encode(['exito' => 'Se ha retirado el importe correctamente']);
            }else{
                echo json_encode(['error' => 'No se pudo realizar el retiro del importe']);
            }
        }
    }else{
        echo json_encode(['error' => 'Falta algun parametro']);
    }
}