<?php
session_start();
include('../conexion/conexionbasededatos.php');
// var_dump($_SESSION);
if (!isset($_SESSION['tipousuario'])) {
    header("Location: inicio-sesion.php"); 
    exit();
}

if ($_SESSION['tipousuario'] != 'Secretario' && $_SESSION['tipousuario'] != 'Administrador'){
    echo 'Error: Acceso no Autorizado.';
    header("Location: ../main/pacientes.php?mensaje=no_autorizado"); 
    exit();
}


$idmedico = null;
$nombremedico     = '';
$dni              = '';
$matricula        = '';
$consultorio      = '';
$direccion        = '';
$telefono         = '';
$correo           = '';
$especialidad     = '';
$estado           = '';
$mensaje          = '';
$error            = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idmedico']) && is_numeric($_POST['idmedico'])) {
    $idmedico = (int) $_POST['idmedico'];
    $sql = "SELECT * FROM medicos 
            WHERE idmedico = ?";
    $stmt = $enlace->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $idmedico);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            $nombremedico = $fila['nombrecompleto'];
            $dni   = $fila['dni'];
            $matricula = $fila['matricula'];
            $consultorio = $fila['consultorio'];
            $direccion = $fila['direcciondomicilio'];
            $telefono = $fila['telefono'];
            $correo = $fila['correo'];
            $especialidad = $fila['especialidad'];
            $estado = $fila['estado'];
        } else {
            $error = "Paciente no encontrado.";
        }
        $stmt->close();
    } else {
        $error = "Error en la consulta de selección.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $idmedico     = (int) $_POST['idmedico'];
    $nuevo_nombre    = trim($_POST['nombremedico']);
    $nuevo_dni       = trim($_POST['dni']);
    $nueva_matricula = trim($_POST['matricula']);
    $nuevo_consultorio = trim($_POST['consultorio']);
    $nueva_direccion = trim($_POST['direccion']);
    $nuevo_telefono = trim($_POST['telefono']);
    $nuevo_correo = trim($_POST['correo']);
    $nueva_especialidad = trim($_POST['especialidad']);
    $nuevo_estado = trim($_POST['estado']);

    if ($nuevo_nombre !== '') {
        $sql = "UPDATE medicos 
                SET nombrecompleto = ?, dni = ?, matricula = ?, consultorio = ?, direcciondomicilio = ?, telefono = ?, correo = ?, especialidad = ?, estado = ?
                WHERE idmedico = ?";
        $stmt = $enlace->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssssssi", $nuevo_nombre, $nuevo_dni, $nueva_matricula, $nuevo_consultorio, $nueva_direccion, $nuevo_telefono, $nuevo_correo, $nueva_especialidad, $nuevo_estado, $idmedico);
            if ($stmt->execute()) {
                header("Location: ../main/medicos.php?mensaje=medico_actualizado");
                exit;
            } else {
                $error = "Error al actualizar el medico.";
            }
            $stmt->close();
        } else {
            $error = "Error en la consulta de actualización.";
        }
    } else {
        $error = "Por favor, completa el nombre del medico.";
    }
}

$enlace->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicos: Modificar Medico</title>
    <link rel="stylesheet" href="../estilos/estilologin.css">
    <link rel="icon" href="../estilos/medicoslista.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
<?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
<div class="container">
    <h1>Modificar Medico</h1>
    
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
        <p><a href="../main/medicos.php" class="button">Volver a la lista de medicos</a></p>
    <?php elseif ($idmedico): ?>
        <form action="modificar-medico.php" method="post">
            <input type="hidden" name="idmedico" value="<?= $idmedico ?>">
    
            <div class="formulario">
                <label for="nombremedico">Nombre del Medico:</label>
                <input type="text" id="nombremedico" name="nombremedico"
                        value="<?= htmlspecialchars($nombremedico) ?>" required>
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni"
                        value="<?= htmlspecialchars($dni) ?>" required>
                <label for="matricula">Matricula:</label>
                <input type="text" id="matricula" name="matricula"
                        value="<?= htmlspecialchars($matricula) ?>" required>
                <label for="consultorio">Consultorio:</label>
                <input type="text" id="consultorio" name="consultorio"
                        value="<?= htmlspecialchars($consultorio) ?>" required>
                <label for="direccion">Direccion de Domicilio:</label>
                <input type="text" id="direccion" name="direccion"
                        value="<?= htmlspecialchars($direccion) ?>" required>
                <label for="telefono">Telefono:</label>
                <input type="text" id="telefono" name="telefono"
                        value="<?= htmlspecialchars($telefono) ?>" required>
                <label for="correo">Correo:</label>
                <input type="text" id="correo" name="correo"
                        value="<?= htmlspecialchars($correo) ?>" required>
                <label for="especialidad">Especialidad:</label>
                <input type="text" id="especialidad" name= "especialidad"
                        value="<?= htmlspecialchars($especialidad) ?>" required>
                <label for="estado">Estado:</label>
                <select type="option" id="estado" name="estado" required>
                <option value="Activo" <?php if ($estado === 'Activo') echo 'selected'; ?>>Activo</option>
                <option value="Inactivo" <?php if ($estado === 'Inactivo') echo 'selected'; ?>>Inactivo</option>
                <option value="Licencia" <?php if ($estado === 'Licencia') echo 'selected'; ?>>Licencia</option>
                </select>
            </div>
            <div class=formulario-botones>
                <button type="submit" name="guardar">Guardar Cambios</button>
                <a href="../main/medicos.php" class="button" onclick="return confirm('¿Estás seguro de que deseas cancelar los cambios?');">Cancelar</a>
            </div>
        </form>
    <?php else: ?>
        <p>No se ha seleccionado ningún medico para modificar.</p>
        <a href="../main/medicos.php" class="button">Volver a la lista de medicos.</a>
    <?php endif; ?>
</div>
    <div class= footer>
    <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>