<?php
require_once './functions.php';
if(isset($_POST["submit"])){
    $dem_prom=demanda_promedio($_POST["demanda"]);
    $modelo_final=correr_modelo($_POST);
    $modelo=$modelo_final["modelo"];
    $pronostico=$modelo_final["pronostico"];
} else {
    die("Favor de cargar datos");
}
$title="Resultados";
require_once './header.php'
?>

<div class="col-sm-8 col-sm-offset-2 text-left">
    <h2>Resultados</h2>
    <div class="row">
        <h4 class="text-center">Modelo obtenido: <strong><?php echo strtoupper($modelo["nombre"]); ?></strong></h4><hr>
        <h4 class="text-center">Pronostico obtenido: <strong><?php echo strtoupper($pronostico["nombre"]); ?></strong></h4><hr>
        <div class="col-sm-5 col-sm-offset-1">
            <h3><?php echo strtoupper($modelo["nombre"]); ?></h3>
            <label for="">Demanda promedio: </label>
            <?php echo $modelo["demanda_promedio"]; ?><br>
            <label for="">Cantidad a pedir (Q):</label>
            <?php echo $modelo["q"]; ?><br>
            <label for="">Ciclo del inventario (T):</label>
            <?php echo $modelo["t"]; ?><br>
            <label for="">Demanda en tiempo de entrega</label>
            <?php echo $modelo["demanda_tao"]; ?><br>
            <label for="">Desviación Est. del tiempo de entrega:</label>
            <?php echo $modelo["sigma_tao"]; ?><br>
            <label for="">Inventario de seguridad (s): </label>
            <?php echo $modelo["s_csl"]; ?><br>
            <label for="">Punto de reorden (R): </label>
            <?php echo $modelo["r_csl"]; ?><br>
            <label for="">Productos no surtidos: </label>
            <?php echo $modelo["no_surtidas"]; ?><br>
        </div>
        <div class="col-sm-5 ">
            <h4>Datos proporcionados</h4>
            <label for="">Costo por pedir(A): </label>
            $<?php echo $_POST["costo_pedido"]; ?><br>
            <label for="">Costo por unidad (C): </label>
            $<?php echo $_POST["costo_unidad"]; ?><br>
            <label for="">Interes mensual: </label>
            <?php echo $_POST["interes"]*100; ?>%<br>
            <label for="">Porcentaje de retencion: </label>
            <?php echo $_POST["ret"]*100; ?>%<br>
            <h4>Análisis de costos anual</h4>
            <label for="">Costo por pedido:</label>
            $<?php echo $modelo["costo_pedido"]; ?><br>
            <label for="">Costo por retención:</label>
            $<?php echo $modelo["costo_retencion"]; ?><br>
            <label for="">Costo total:</label>
            $<?php echo $modelo["costo_total"]; ?>
        </div>
    </div><hr>
    <div class="row">
        <div class="col-sm-5 col-sm-offset-1">
            <h3>Pronosticos</h3>
            <table class="table table-striped">
                <tr>
                    <th>Periodo</th>
                    <th>Cantidad</th>
                </tr>
                <?php
                    $output="";
                    $forecast=array_slice($pronostico["mt"],-12,null,true);
                    foreach ($forecast as $key => $value){
                        $cantidad=ceil($value);
                        $periodo=$key+1;
                        $output.= "<tr><td>{$periodo}</td>";
                        $output.="<td>{$cantidad}</td></tr>";
                    }
                    echo $output;
                ?>

            </table>
        </div>
        <div class="col-sm-5 ">
            <br><br><br>
            <label for="">MAPE:</label>
            <?php echo pretty_number($pronostico["mape"]*100); ?><br>
            <label for="">CME:</label>
            <?php echo pretty_number($pronostico["cme"]); ?>
        </div>
    </div>


</div>
