<?php

switch($_SERVER['REQUEST_METHOD'])
{   
    case 'GET':
        if(isset($_GET['consulta'])) 
        {
            require_once "./ConsultaMovimientos.php";
            switch ($_GET['consulta']) {
                case 'a':
                    GET_consultaTotalDepositado();
                break;
                case 'b':
                    GET_consultaDepositosDeUsuario();
                break;
                case 'c':
                    GET_consultaDepositosEntreFechas();
                break;
                case 'd':
                    GET_consultaDepositosPorTipoDeCuenta();
                break;
                case 'e':
                    GET_consultaDepositosPorMoneda();
                break;
            }
        }
        break;

    case 'POST':
        if(isset($_POST['action'])) 
        {
            switch ($_POST['action']) {
                case 'cuentaAlta':
                    require_once "./CuentaAlta.php";
                    POST_cuentaAlta();
                break;
                
                case 'consultarCuenta':
                    require_once "./ConsultarCuenta.php";
                    POST_consultarCuenta();
                break;

                case 'deposito':
                    require_once "./DepositoCuenta.php";
                    POST_depositoCuenta();
                break;

                case 'retiro':
                    require_once "./RetiroCuenta.php";
                    POST_retiroCuenta();
                break;

                case 'ajuste':
                    require_once "./AjusteCuenta.php";
                    POST_ajusteCuenta();
                break;
            }
        }
    break;

    case 'PUT':
        require_once "./ModificarCuenta.php";
        PUT_modificarCuenta();
    break;
}
?>