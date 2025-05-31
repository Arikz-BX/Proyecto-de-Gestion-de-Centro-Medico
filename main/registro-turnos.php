<?php
session_start();
var_dump($_SESSION);
include('../conexion/conexionbasededatos.php');
include('../funciones/funcionesturnos.php');
$medicos = obtenerMedicos($enlace);
$pacientes = obtenerPacientes($enlace);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Turno</title>
    <link rel="stylesheet" href="../estilos/estilologin.css">
    <link rel="icon" href="../estilos/medicoslista.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1>Registro de Turno</h1>
        <form action="../acciones/registrar_turno.php" method="post">
            <div>
                <label for="idmedico">Médico:</label>
                <select name="idmedico" id="idmedico" required>
                    <?php foreach ($medicos as $medico) : ?>
                        <option value="<?php echo $medico['idmedico']; ?>"><?php echo $medico['nombrecompleto']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="idpaciente">Paciente:</label>
                <select name="idpaciente" id="idpaciente" required>
                    <?php foreach ($pacientes as $paciente) : ?>
                        <option value="<?php echo $paciente['idpaciente']; ?>"><?php echo $paciente['nombrepaciente']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="fecha">Fecha:</label>
                <input type="datetime-local" name="fecha" id="fecha" required>
            </div>
            <div>
              <label for="lugar">Lugar:</label>
              <select name="lugar" id="lugar" required>
                  <?php foreach ($medicos as $medico) : ?>
                      <option value="<?php echo $medico['consultorio']; ?>"><?php echo $medico['consultorio']; ?></option>
                  <?php endforeach; ?>
              </select>
            </div>
            <div>
                <label for="observacion">Observación:</label>
                <textarea name="observacion" id="observacion"></textarea>
            </div>
            <button type="submit">Registrar Turno</button>
            <a href="../main/turnos.php" class="button" onclick="return confirm('¿Estás seguro de que deseas cancelar el registro del Turno?');">Cancelar</a>
        </form>
    </div>
</body>

</html>

