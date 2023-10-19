<?php
require_once './entidades/Cuenta.php';
require_once './entidades/Deposito.php';
require_once './entidades/Retiro.php';
require_once './entidades/Ajuste.php';

function POST_ajusteCuenta() 
{
    if (isset($_POST['nroDeOperacion'],$_POST['motivo'], $_POST['monto']))
    {    
        $nroDeOperacion = $_POST['nroDeOperacion'];
        $motivo = $_POST['motivo'];
        $monto = $_POST['monto'];
        $operacionNombre = null;
            
        $operacionObjeto = Deposito::traerDeposito($nroDeOperacion);
        
        if($operacionObjeto != null) {
            $operacionNombre = 'DEPOSITO';
            $validacionDeMonto = Ajuste::validarMontoAjuste($operacionObjeto, $monto);
        }else {
            $operacionObjeto = Retiro::traerRetiro($nroDeOperacion);
            $operacionNombre = 'EXTRACCION';
            $validacionDeMonto = Ajuste::validarMontoAjuste($operacionObjeto, $monto);
        }
        
        if($validacionDeMonto && Ajuste::registrarAjuste($nroDeOperacion, $operacionNombre, $motivo, $monto)) 
        {
            $nroDeCuenta = $operacionObjeto->cuenta->numeroDeCuenta;
            $cuenta = Cuenta::traerCuenta($nroDeCuenta);
            $saldoActualizado = $cuenta->saldo + $monto;
            Cuenta::AltaUsuario($cuenta->nombre, $cuenta->apellido, $cuenta->tipoDocumento, $cuenta->nroDocumento, $cuenta->email, $cuenta->tipoDeCuenta, $cuenta->moneda, $saldoActualizado, $cuenta->imagen);
            echo json_encode(['exito' => 'Se realizo el ajuste correctamente']);
        }else{
            echo json_encode(['error' => 'No se pudo realizar el ajuste - verifique los datos enviados']);
        }
    }
}
