<?php
session_start();
include('../conexion/conexionbasededatos.php');
if (!isset($_SESSION['tipousuario'])) {
    header("Location: index.php"); 
    exit();
}


$idusuario       = null;
$nombreusuario   = '';
$nombrecompleto  = '';
$tipousuario     = '';
$mensaje         = '';
$error           = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $idusuario = (int) $_POST['id'];
    $sql = "SELECT idusuario, nombreusuario, nombrecompleto, tipousuario 
            FROM usuarios 
            WHERE idusuario = ?";
    $stmt = $enlace->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $idusuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            $nombreusuario = $fila['nombreusuario'];
            $nombrecompleto = $fila['nombrecompleto'];
            $tipousuario   = $fila['tipousuario'];
        } else {
            $error = "Usuario no encontrado.";
        }
        $stmt->close();
    } else {
        $error = "Error en la consulta de selección.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $idusuario       = (int) $_POST['idusuario'];
    $nuevo_nombreusuario    = trim($_POST['nombreusuario']);
    $nuevo_nombrecompleto   = trim($_POST['nombrecompleto']);
    $nuevo_tipousu   = trim($_POST['tipousuario']);

    if ($nuevo_nombre !== '' && $nuevo_tipousu !== '') {
        $sql = "UPDATE usuarios 
                SET nombreusuario = ?, nombrecompleto = ?, tipousuario = ? 
                WHERE idusuario = ?";
        $stmt = $enlace->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssi", $nuevo_nombreusuario, $nuevo_nombrecompleto, $nuevo_tipousu, $idusuario);
            if ($stmt->execute()) {
                header("Location: ../main/usuarios.php?success=usuario_modificado");
                exit;
            } else {
                $error = "Error al actualizar el usuario.";
            }
            $stmt->close();
        } else {
            $error = "Error en la consulta de actualización.";
        }
    } else {
        $error = "Por favor, completa todos los campos.";
    }
}

$enlace->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios: Modificar Usuario</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link rel="icon" href="../estilos/ususarios.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
    <?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
    <div class="container">
        <h1>Modificar Usuario</h1>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
            <p><a href="../main/usuarios.php" class="button">Volver a la lista de usuarios</a></p>
        <?php elseif ($idusuario): ?>
            <form action="modificar-usuario.php" method="post">
                <input type="hidden" name="idusuario" value="<?= $idusuario ?>">

                <?php if($_SESSION['tipousuario'] == 'Secretario' && $tipousuario == 'Secretario'){ //Este Condicional es solo para los Secretarios.
                    //Esto evita que los Secretarios se modifiquen datos entre ellos. 
                    if ($tipousuario === 'Administrador'||$tipousuario === 'Secretario') { ?> 
                    <div class="username">
                        <label for="nombreusuario">Nombre de Usuario:</label>
                        <input type="text" name="nombreusuario" value="<?php echo htmlspecialchars($nombreusuario); ?>" readonly class="form-control-plaintext">
                        <small class="text-muted">No puedes modificar datos de otro Secretario o del Administrador.</small>
                    </div>
                    <div class="nombrepersonal">
                        <label for="nombrecompleto">Nombre Completo:</label>
                        <input type="text" name="nombrecompleto" value="<?php echo htmlspecialchars($nombrecompleto); ?>" readonly class="form-control-plaintext">
                        <small class="text-muted">No puedes modificar datos de otro Secretario o del Administrador.</small>
                    </div>
                    <?php
                    } else {
                    ?>
                    <div class="formulario">
                    <label for="nombreusuario">Nombre de Usuario:</label>
                    <input type="text" id="nombreusuario" name="nombreusuario"
                        value="<?= htmlspecialchars($nombreusuario) ?>" required>
                    <label for="nombrecompleto">Nombre Completo:</label>
                    <input type="text" id="nombreusuario" name="nombreusuario"
                        value="<?= htmlspecialchars($nombreusuario) ?>" required>
                    </div>
                    
                    <?php
                    }
                }
                ?>
                <?php if (isset($_SESSION['tipousuario']) && $_SESSION['tipousuario'] == 'Administrador') { 
                    if ($tipousuario === 'Administrador' && $_SESSION['idusuario'] === $idusuario) { ?>
                    <div class="formulario">
                    <label for="nombreusuario">Nombre de Usuario:</label>
                    <input type="text" id="nombreusuario" name="nombreusuario"
                        value="<?= htmlspecialchars($nombreusuario) ?>" required>
                    <label for="nombrecompleto">Nombre Completo:</label>
                    <input type="text" id="nombrecompleto" name="nombrecompleto"
                        value="<?= htmlspecialchars($nombrecompleto) ?>" required>
                    <label for="tipousuario">Tipo de Usuario:</label>
                        <input type="text" id="tipousuario" name="tipousuario" value="<?php echo $tipousuario; ?>" readonly class="form-control-plaintext">
                        <small class="text-muted">No puedes cambiar tu tipo de usuario.</small>
                    </div>
                <?php
                } else {
                    ?>
                    <label for="nombreusuario">Nombre de Usuario:</label>
                    <input type="text" id="nombreusuario" name="nombreusuario"
                        value="<?= htmlspecialchars($nombreusuario) ?>" required>
                    <label for="nombrecompleto">Nombre Completo:</label>
                    <input type="text" id="nombrecompleto" name="nombrecompleto"
                        value="<?= htmlspecialchars($nombrecompleto) ?>" required>
                    <div class="formulario">
                    <label for="tipousuario">Tipo de Usuario:</label>
                    <select id="tipousuario" name="tipousuario" required>
                        <option value="Medico" <?php if ($tipousuario === 'Medico') echo 'selected'; ?>>Médico</option>
                        <option value="Secretario" <?php if ($tipousuario === 'Secretario') echo 'selected'; ?>>Secretario</option>
                        </select>
                    </div> 
                    <?php } 
                }
                ?>
                <div class="formulario-botones">
                    <button type="submit" name="guardar">Guardar Cambios</button>
                    <a href="../main/usuarios.php" class="button">Cancelar</a>
                </div>
            </form>
        <?php else: ?>
            <p>No se ha seleccionado ningún usuario para modificar.</p>
            <a href="../main/usuarios.php" class="button">Volver a la lista de usuarios</a>
        <?php endif; ?>
    </div>
<div class= footer>
        <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>



