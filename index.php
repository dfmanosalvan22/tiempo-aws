<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Controller/ControladorPrincipal.php';

$controlador = new ControladorPrincipal();
$controlador->ejecutar();
