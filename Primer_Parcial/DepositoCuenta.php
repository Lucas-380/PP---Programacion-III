<?php
require_once './entidades/Cuenta.php';
require_once './entidades/Deposito.php';

function POST_depositoCuenta() 
{
    if (isset($_POST['tipoDeCuenta']) && isset($_POST['nroDeCuenta']) && isset($_POST['moneda']) && isset($_POST['importe'])) 
    {
        $tipoDeCuenta = $_POST['tipoDeCuenta'];
        $nroDeCuenta = $_POST['nroDeCuenta'];
        $moneda = $_POST['moneda'];
        $importe = $_POST['importe'];

        if($tipoDeCuenta != null && $nroDeCuenta != null && $moneda != null && $importe != null)
        {
            $cuentaActualizada = Cuenta::depositarImporte($tipoDeCuenta, $nroDeCuenta, $moneda, $importe);

            if($cuentaActualizada != null && Deposito::registrarDeposito($cuentaActualizada, $importe)) {
                echo json_encode(['exito' => 'Deposito realizado correctamente']);
            }else{
                echo json_encode(['error' => 'El deposito no se pudo realizar - compruebe los datos de la cuenta']);
            }
        }
    }else{
        echo json_encode(['error' => 'Falta algun parametro']);
    }
}