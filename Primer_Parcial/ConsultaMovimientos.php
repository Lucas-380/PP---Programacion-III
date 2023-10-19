<?php

require_once './entidades/Deposito.php';

function GET_consultaTotalDepositado() {
    if (isset($_GET['fecha'])) {
        $fecha = $_GET['fecha'];
        $depositos = Deposito::consultarDepositos($fecha);
    }else{
        $depositos = Deposito::consultarDepositos();
    }

    if($depositos != []){
        Deposito::mostrarTotalDeMontos($depositos);
    }else{
        echo json_encode(['error' => 'No se pudo listar los depositos - verifique la fecha']);
    }
}

function GET_consultaDepositosDeUsuario() {
    if (isset($_GET['nroDeCuenta'])) {
        $usuario = $_GET['nroDeCuenta'];
        $depositos = Deposito::traerDepositosDeUsuario($usuario);
        if($depositos != []){
            Deposito::listarDepositos($depositos);
        }else{
            echo json_encode(['error' => 'No se pudo listar los depositos - verifique el nro de cuenta']);
        }
    }
}

function GET_consultaDepositosEntreFechas() {
    if (isset($_GET['fechaInicio']) && isset($_GET['fechaFinal']))
    {
        $fechaInicio = $_GET['fechaInicio'];
        $fechaFinal = $_GET['fechaFinal'];
        $depositosEntreFechas = Deposito::traerVentasEntreFechas($fechaInicio, $fechaFinal);
        $depositosEntreFechas = Deposito::ordenarPorNombre($depositosEntreFechas);
        if ($depositosEntreFechas != []) {
            Deposito::listarDepositos($depositosEntreFechas);
        }else{
            echo json_encode(['error' => 'No se encontraron ventas en el rango de fechas especificado.']);
        }
    }
}

function GET_consultaDepositosPorTipoDeCuenta() {
    if (isset($_GET['tipoDeCuenta'])) {
        $tipoDeCuenta = $_GET['tipoDeCuenta'];
        $depositos = Deposito::traerDepositosPorTipoDeCuenta(strtoupper($tipoDeCuenta));
        if($depositos != []){
            Deposito::listarDepositos($depositos);
        }else{
            echo json_encode(['error' => 'No se pudo listar los depositos - verifique el tipo de cuenta']);
        }
    }
}

function GET_consultaDepositosPorMoneda() {
    if (isset($_GET['moneda'])) {
        $moneda = $_GET['moneda'];
        $depositos = Deposito::traerDepositosPorMoneda(strtoupper($moneda));
        if($depositos != []){
            Deposito::listarDepositos($depositos);
        }else{
            echo json_encode(['error' => 'No se pudo listar los depositos - verifique la moneda']);
        }
    }
}