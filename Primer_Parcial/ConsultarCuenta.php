<?php
require_once './entidades/Cuenta.php';

function POST_consultarCuenta() 
{
    if (isset($_POST['tipoDeCuenta']) && isset($_POST['nroDeCuenta'])) 
    {
        $tipoDeCuenta = $_POST['tipoDeCuenta'];
        $nroDeCuenta = $_POST['nroDeCuenta'];

        if($tipoDeCuenta != null && $nroDeCuenta != null)
        {
            if(!Cuenta::consultarCuenta($tipoDeCuenta, $nroDeCuenta)) {
                echo json_encode(['error' => 'Tipo de cuenta y Nro de cuenta no registrados']);
            }
        }
    }else{
        echo json_encode(['error' => 'Falta algun parametro']);
    }
}