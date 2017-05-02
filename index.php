<?php
$title="Carga de datos";
require_once './header.php';
?>
    <div class="col-sm-4 col-sm-offset-2 text-left">
        <h3>Ingresar valores</h3>
        <form action="resultados.php" method="post">
            <label for="demanda">Demanda</label>
            <p class="text-muted">*Favor de ingresar las unidades en meses</p>
            <table class="table table-striped" id="dynamic_field">
                <tr>
                    <th>Periodo</th>
                    <th>Cantidad</th>
                    <th></th>
                </tr>
                <tr>
                    <td>1</td>
                    <td><input type="number" step=".01" name="demanda[]" class="form-control" required></td>
                     <td><button type="button" name="add" id="add" class="btn btn-success pull-right">+</button></td>
                </tr>
            </table>
            <h3></h3>
            <label for="">Costo por pedido</label>
            <input type="number" step=".01" name="costo_pedido" class="form-control" required><br>
            <label for="">Costo por unidad</label>
            <input type="number" step=".01" name="costo_unidad" class="form-control" required><br>
            <label for="">Interes</label>
            <input type="number" step=".01" name="interes" class="form-control" required><br>
            <p class="text-muted">Ingresar los siguientes datos si se cuenta con ellos</p>
            <label for="">Lead Time</label>
            <input type="number" step=".01" name="lt" class="form-control" required><br>
            <label for="">Fill Rate</label>
            <input type="number" step=".01" name="fr" class="form-control" required><br>
            <label for="">Objetivo de nivel de servicio (CSL)</label>
            <input type="number" step=".01" name="alpha" class="form-control" required><br>
            <label for="">Nivel de retención</label>
            <input type="number" step=".01" name="ret" class="form-control" required><br>
            <input type="submit" name="submit" class="btn btn-success center-block btn-md" value="Calcular"><br>
        </form>
    </div>
  </div>
</div>

<script src="./js/dynamic_rows.js"></script>
