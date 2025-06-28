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
    header("Location: ../main/listado_pacientes.php?info=no_autorizado"); 
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
$readonly = ($estado === 'Dado de Alta') ? 'readonly' : '';
$disabled = ($estado === 'Dado de Alta') ? 'disabled' : '';

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
            $stmt->bind_param("ssssssssi", $nuevo_nombre, $nuevo_dni, $nueva_obrasocial, $nueva_direccion, $nuevo_telefono, $nuevo_correo, $nuevo_estado, $nuevas_notas, $idpaciente);
            if ($stmt->execute()) {
                header("Location: ../main/listado_pacientes.php?success=paciente_modificado");
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
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link rel="icon" href="../estilos/medicoslista.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
    <?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
    <div class="container">
        <h1 class="paciente">Modificar Paciente</h1>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
            <p><a href="../main/pacientes.php" class="button">Volver a la lista de pacientes</a></p>
        <?php elseif ($idpaciente): ?>
            <form id="formularioPaciente" action="modificar-paciente.php" method="post">
                <input type="hidden" name="idpaciente" value="<?= $idpaciente ?>">

                <div class="formulario">
                    <label for="nombrepaciente">Nombre del Paciente:</label>
                    <input type="text" id="nombrepaciente" name="nombrepaciente" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$"
                            value="<?= htmlspecialchars($nombrepaciente) ?>" maxlength="255" class="form-control" required <?= $disabled ?>>
                    <label for="dni">DNI:</label>
                    <input type="text" id="dni" name="dni"
                            value="<?= htmlspecialchars($dni) ?>" maxlength="10" class="form-control" required <?= $disabled ?>>
                    <label for="obrasocial">Obra Social:</label>
                    <input type="text" id="obrasocial" name="obrasocial"
                            value="<?= htmlspecialchars($obrasocial) ?>" maxlength="255" class="form-control" required <?= $disabled ?>>
                    <label for="direccion">Direccion:</label>
                    <input type="text" id="direccion" name="direccion"
                            value="<?= htmlspecialchars($direccion) ?>" maxlength="255" class="form-control" required <?= $disabled ?>>
                    <label for="telefono">Telefono:</label>
                    <input type="text" id="telefono" name="telefono"
                            value="<?= htmlspecialchars($telefono) ?>" maxlength="10" class="form-control" required <?= $disabled ?>>
                    <label for="correoelectronico">Correo:</label>
                    <input type="text" id="correoelectronico" name="correoelectronico"
                            value="<?= htmlspecialchars($correo) ?>" maxlength="255" class="form-control" required <?= $disabled ?>>
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" class="form-control" required <?= $disabled ?>>
                    <option value="En Espera" <?php if ($estado === 'En Espera') echo 'selected'; ?>>En Espera</option>
                    <option value="En Tratamiento" <?php if ($estado === 'En Tratamiento') echo 'selected'; ?>>En Tratamiento</option>
                    <option value="Dado de Alta" <?php if ($estado === 'Dado de Alta') echo 'selected'; ?>>Dado de Alta</option>
                    </select>
                    <label for="notas">Notas:</label>
                    <textarea id="notas" name="notas" maxlength="650" rows="3" class="form-control" required <?= $disabled ?>><?= htmlspecialchars($notas) ?> </textarea>
                </div>
                <?php if (!$readonly): ?>
                    <div class="formulario">
                        <button type="submit" name="guardar">Guardar Cambios</button>
                        <!-- <a href="../main/listado_pacientes.php" class="button" onclick="return confirm('¿Estás seguro de que deseas cancelar los cambios?');">Cancelar</a> -->
                    </div>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <p>No se ha seleccionado ningún paciente para modificar.</p>
            <a href="../main/listado_pacientes.php" class="button">Volver a la lista de pacientes</a>
        <?php endif; ?>
    </div>
    <div class= footer>
        <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
<script>
    const form = document.getElementById('formularioPaciente');
    let formInicial = new FormData(form);

    window.addEventListener('DOMContentLoaded', () => {
        formInicial = new FormData(form);
    });

    function detectaCambios() {
        const formActual = new FormData(form);
        for (let [key, value] of formInicial.entries()) {
            if (formActual.get(key) !== value) {
                return true;
            }
        }
        return false;
    }

    const enlacesRetorno = document.querySelectorAll('a.boton-retorno');
    enlacesRetorno.forEach(enlace => {
        enlace.addEventListener('click', function (e) {
            if (detectaCambios()) {
                const confirmacion = confirm("¿Estás seguro de que deseas cancelar los cambios?");
                if (!confirmacion) {
                    e.preventDefault();
                }
            }
        });
    });
</script>
</body>
</html>