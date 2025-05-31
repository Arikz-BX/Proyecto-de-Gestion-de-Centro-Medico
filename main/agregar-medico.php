<?php
include('../conexion/conexionbasededatos.php');
function listMedicos($enlace)
{
    $medicossql = "SELECT idmedico, matricula FROM medicos";
    $resultado = $enlace->query($medicossql);
    return $resultado;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado: Agregar Médico</title>
    <link rel="stylesheet" href="../estilos/estilologin.css">
    <link rel="icon" href="../estilos/medicoslista.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1>Agregar Médico</h1>
        <form action="../acciones/agregar_medico.php" method="post">
            <div class="form-group">
            <label for="matricula">Matricula del Médico:</label>
                <select name="matricula" id="matricula" required>
                    <?php if ($resultado && $resultado->num_rows > 0) {
                        while ($medico = $resultado->fetch_assoc()) {
                            echo "<option value='" . $medico['matricula'] . "'>" . $medico['idmedico'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay médicos para registrar.</option>";
                    }
                    ?>
            </select>
                <label for="nombrecompleto">Nombre Medico:</label>
                <input type="text" id="nombrecompleto" name="nombrecompleto" required>
            </div>
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" minlength="8" name="dni" required>
            </div>
            <div class="form-group">
                <label for="consultorio">Consultorio:</label>
                <input type="text" id="consultorio" name="consultorio" required>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección de Domicilio:</label>
                <input type="text" id="direccion" name="direccion" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" minlength="9" name="telefono" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div class="form-group">
                <label for="especialidad">Especialidad:</label>
                <input type="text" id="especialidad" name="especialidad" required>
            </div>
            <button type="submit" class="button">Agregar Médico</button>
            <a href="../main/medicos.php" class="button button-secondary" onclick="return confirm('¿Estás seguro de que deseas cancelar el Registro del Medico?');">Cancelar</a>
        </form>
    </div>
</body>
</html>