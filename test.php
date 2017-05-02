<?php
require_once './functions.php';
$datos=["demanda"=>[20,30,50,10,25,30,10,15,20,25],"alpha"=>.95];
//promedios_moviles($datos);
$temp=array_slice($datos["demanda"],-2);
print_r($temp);
?>
