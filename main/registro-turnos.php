<?php
session_start();
/*var_dump($_SESSION);*/
include('../conexion/conexionbasededatos.php');
include('../funciones/funcionesturnos.php');
$medicos = obtenerMedicos($enlace);
$pacientes = obtenerPacientes($enlace);
if (isset($_GET['error'])) {
    $error_code = $_GET['error'];

    // Se usa un SWITCH para cada caso de error.
    switch ($error_code) {
        case '':
            $mensaje_error = "ERROR: .";
            break;
        case '':
            $mensaje_error = "ERROR: .";
            break;
        case 'turno_fuera_agenda':
            $mensaje_error = "ERROR: El turno está fuera del horario de agenda del médico.";
            break;
        case 'fallo_de_registro':
            $mensaje_error = "ERROR: Error al registrar turno:  . $stmt->error.";
            break;
        case 'error_consulta_db':
            $mensaje_error = "ERROR: Error en la preparación de la consulta: . $enlace->error . Inténtelo más tarde.";
            break;
        case 'consultorio_no_encontrado':
            $mensaje_error = "ERROR: No se encontró el consultorio del médico.";
            break;
        case 'consultorio_no_encontrado':
            $mensaje_error = "ERROR: No se recibieron todos los datos del Formulario.";
            break;
        default:
            $mensaje_error = "ERROR: Algo salió mal. Por favor, inténtelo de nuevo.";
            break;
    }
    echo "<script>";
    echo "alert('" . htmlspecialchars($mensaje_error, ENT_QUOTES, 'UTF-8'). "');"; //Linea cambiada a peticion de los Profesores, caja de alerta para el error.
    echo "window.history.replaceState({}, document.title, window.location.pathname);"; //Linea de Codigo usada para que el ALERT no se muestre al recargar la misma pagina.
    echo "</script>";  
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Turno</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link rel="icon" href="../estilos/medicoslista.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<body>
    <?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
    <div class="container">
        <h1>Registro de Turno</h1>
        <form action="../acciones/registrar_turno.php" method="post">
            <div class="formulario">
                <label for="idmedico">Médico:</label>
                <select name="idmedico" id="idmedico" required>
                    <?php foreach ($medicos as $medico) : ?>
                        <option value="<?php echo $medico['idmedico']; ?>"><?php echo $medico['nombrecompleto']; ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="idpaciente">Paciente:</label>
                <select name="idpaciente" id="idpaciente" required>
                    <?php foreach ($pacientes as $paciente) : ?>
                        <option value="<?php echo $paciente['idpaciente']; ?>"><?php echo $paciente['nombrepaciente']; ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="fecha">Fecha:</label>
                <input type="datetime-local" name="fecha" id="fecha" required>
              <label for="lugar">Lugar:</label>
              <select name="lugar" id="lugar" required>
                  <?php foreach ($medicos as $medico) : ?>
                      <option value="<?php echo $medico['consultorio']; ?>"><?php echo $medico['consultorio']; ?></option>
                  <?php endforeach; ?>
              </select>
                <label for="observacion">Observación:</label>
                <textarea name="observacion" id="observacion"></textarea>
                <button type="submit">Registrar Turno</button>
                <a href="../main/turnos.php" class="button" onclick="return confirm('¿Estás seguro de que deseas cancelar el registro del Turno?');">Cancelar</a>
            </div>
        </form>
    </div>
<div class= footer>
        <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>

