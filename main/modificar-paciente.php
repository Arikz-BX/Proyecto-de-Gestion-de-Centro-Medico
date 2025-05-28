<?php
session_start();
include('../conexion/conexionbasededatos.php');
var_dump($_SESSION);
if (!isset($_SESSION['tipousuario'])) {
    header("Location: inicio-sesion.php"); 
    exit();
}

if ($_SESSION['tipousuario'] != 'Secretario' && $_SESSION['tipousuario'] != 'Administrador'){
    echo 'Error: Acceso no Autorizado.';
    header("Location: ../main/pacientes.php?mensaje=no_autorizado"); 
    exit();
}


$idpaciente = null;
$nombrepaciente   = '';
$dni              = '';
$obrasocial       = '';
$direccion        = '';
$telefono         = '';
$correo           = '';
$estado           = '';
$notas            = '';
$mensaje          = '';
$error            = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idpaciente']) && is_numeric($_POST['idpaciente'])) {
    $idpaciente = (int) $_POST['idpaciente'];
    $sql = "SELECT * FROM pacientes 
            WHERE idpaciente = ?";
    $stmt = $enlace->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $idpaciente);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            $nombrepaciente = $fila['nombrepaciente'];
            $dni   = $fila['dni'];
            $obrasocial = $fila['obrasocial'];
            $direccion = $fila['direccion'];
            $telefono = $fila['telefono'];
            $correo = $fila['correoelectronico'];
            $notas = $fila['notas'];
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
    $idpaciente      = (int) $_POST['idpaciente'];
    $nuevo_nombre    = trim($_POST['nombrepaciente']);
    $nuevo_dni       = trim($_POST['dni']);
    $nueva_obrasocial = trim($_POST['obrasocial']);
    $nueva_direccion = trim($_POST['direccion']);
    $nuevo_telefono = trim($_POST['telefono']);
    $nuevo_correo = trim($_POST['correoelectronico']);
    $nuevo_estado = trim($_POST['estado']);
    $nuevas_notas = trim($_POST['notas']);

    if ($nuevo_nombre !== '') {
        $sql = "UPDATE pacientes 
                SET nombrepaciente = ?, dni = ?, obrasocial = ?, direccion = ?, telefono = ?, correoelectronico = ?, estado = ?, notas = ?
                WHERE idpaciente = ?";
        $stmt = $enlace->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssssssi", $nuevo_nombre, $idpaciente);
            if ($stmt->execute()) {
                header("Location: ../main/pacientes.php?mensaje=paciente_actualizado");
                exit;
            } else {
                $error = "Error al actualizar el paciente.";
            }
            $stmt->close();
        } else {
            $error = "Error en la consulta de actualización.";
        }
    } else {
        $error = "Por favor, completa el nombre del paciente.";
    }
}

$enlace->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes: Modificar Paciente</title>
    <link rel="stylesheet" href="../estilos/estilologin.css">
    <link rel="icon" href="../estilos/medicoslista.ico">
</head>
<body>
    <div class="container">
        <h1>Modificar Paciente</h1>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
            <p><a href="../main/pacientes.php" class="button">Volver a la lista de pacientes</a></p>
        <?php elseif ($idpaciente): ?>
            <form action="modificar-paciente.php" method="post">
                <input type="hidden" name="idpaciente" value="<?= $idpaciente ?>">

                <div class="form-group">
                    <label for="nombrepaciente">Nombre del Paciente:</label>
                    <input type="text" id="nombrepaciente" name="nombrepaciente"
                            value="<?= htmlspecialchars($nombrepaciente) ?>" required>
                </div>
                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <input type="text" id="dni" name="dni"
                            value="<?= htmlspecialchars($dni) ?>" required>
                </div>
                <div class="form-group">
                    <label for="obrasocial">Obra Social:</label>
                    <input type="text" id="obrasocial" name="obrasocial"
                            value="<?= htmlspecialchars($obrasocial) ?>" required>
                </div>
                 <div class="form-group">
                    <label for="direccion">Direccion:</label>
                    <input type="text" id="direccion" name="direccion"
                            value="<?= htmlspecialchars($direccion) ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Telefono:</label>
                    <input type="text" id="telefono" name="telefono"
                            value="<?= htmlspecialchars($telefono) ?>" required>
                </div>
                <div class="form-group">
                    <label for="correoelectronico">Correo:</label>
                    <input type="text" id="correoelectronico" name="correoelectronico"
                            value="<?= htmlspecialchars($correo) ?>" required>
                </div>
                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select type="option" id="estado" name="estado" required>
                    <option value="En Espera" <?php if ($estado === 'En Espera') echo 'selected'; ?>>En Espera</option>
                    <option value="En Tratamiento" <?php if ($estado === 'En Tratamiento') echo 'selected'; ?>>En Tratamiento</option>
                    <option value="Dado de Alta" <?php if ($estado === 'Dado de Alta') echo 'selected'; ?>>Dado de Alta</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="notas">Notas:</label>
                    <input type="text" id="notas" name="notas"
                            value="<?= htmlspecialchars($notas) ?>" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="guardar">Guardar Cambios</button>
                    <a href="../main/pacientes.php" class="button" onclick="return confirm('¿Estás seguro de que deseas cancelar los cambios?');">Cancelar</a>
                </div>
            </form>
        <?php else: ?>
            <p>No se ha seleccionado ningún paciente para modificar.</p>
            <a href="../main/listado-pacientes.php" class="button">Volver a la lista de pacientes</a>
        <?php endif; ?>
    </div>
</body>
</html>