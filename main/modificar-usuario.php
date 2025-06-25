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
$error_interno   = '';
//Condicionales de Visualizacion
$id_usuario_sesion = isset($_SESSION['idusuario']) ? (int)$_SESSION['idusuario'] : 0;
$es_admin_logueado = ($_SESSION['tipousuario'] == 'Administrador');
$es_secretario_logueado = ($_SESSION['tipousuario'] == 'Secretario');
$es_medico_logueado = ($_SESSION['tipousuario'] == 'Medico');


$idusuario_a_cargar = null;
if (isset($_POST['idusuario']) && !empty($_POST['idusuario'])) {
    $idusuario_a_cargar = (int)$_POST['idusuario']; // General para todos los datos cargados por POST (Desde el form action) //
} elseif ($id_usuario_sesion > 0) {
    $idusuario_a_cargar = $id_usuario_sesion; // Especifico para el boton "Mi Perfil" o cuando un Usuario se modifica a si mismo en Usuarios.php //
} else {
    /*header("Location: ../main/usuarios.php?error=a");*/
    $error_interno = "No se ha especificado un usuario para modificar o la sesión no está activa.";
}
try {
    $sql = "SELECT idusuario, nombreusuario, nombrecompleto, tipousuario, estado 
            FROM usuarios 
            WHERE idusuario = ?";
    $stmt = $enlace->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("fallo_consulta_preparar"); 
    }
    
    $stmt->bind_param("i", $idusuario_a_cargar);
    $stmt->execute();
    
    $resultado_carga = $stmt->get_result();

    if ($resultado_carga->num_rows === 1) {
        $fila_usuario = $resultado_carga->fetch_assoc();
        
        $idusuario = $fila_usuario['idusuario']; // Este es el ID del usuario cuyo perfil se muestra/modifica
        $nombreusuario = $fila_usuario['nombreusuario'];
        $nombrecompleto = $fila_usuario['nombrecompleto'];
        $tipousuario = $fila_usuario['tipousuario'];
        $estado_usuario = $fila_usuario['estado'];

        // --- VERIFICACIÓN DE PERMISOS PARA ACCEDER/VER EL FORMULARIO (en la carga inicial) ---
        $es_propio_usuario = ($idusuario == $id_usuario_sesion); // Comprueba si el cargado es el propio
        $puede_ver_formulario = false; // Se inicializa a false y se pone a true si se cumplen condiciones

        if ($es_admin_logueado) {
            // Admin logueado puede ver todo
            if ($tipousuario === 'Administrador' && !$es_propio_usuario){ 
                header("Location: ../main/usuarios.php?error=usuario_modificar_admin_denegado");
                exit; 
            }
            $puede_ver_formulario = true; // Si es admin y no está modificando a otro admin
        } elseif ($es_secretario_logueado) {
            // Un Secretario puede ver su propio perfil o el de un Médico
            if ($es_propio_usuario || $tipousuario == 'Medico') {
                $puede_ver_formulario = true;
            } else {
                // Secretario intentando ver a otro Secretario o Administrador
                header("Location: ../main/usuarios.php?error=usuario_modificar_denegado");
                exit;
            } 
        } elseif ($es_medico_logueado) {
            // Un Médico solo puede ver su propio perfil
            if ($es_propio_usuario) {
                $puede_ver_formulario = true;
            } else {
                // Médico intentando ver a otro usuario
                header("Location: ../main/index.php?error=usuario_modificar_denegado");
                exit;
            }
        } else {
            header("Location: ../main/inicio-sesion.php"); 
            exit;
        } 
        if (!$puede_ver_formulario) {
            header("Location: ../main/usuarios.php?error=usuario_modificar_denegado");
            exit;
        }
        $stmt->close(); 
    } else { 
        header("Location: ../main/usuarios.php?error=usuario_no_encontrado");
        exit;
    }

} catch (Exception $e) {
    error_log("Error de carga de usuario en modificar-usuario.php: " . $e->getMessage()); 
    header("Location: ../main/usuarios.php?error=" . urlencode($e->getMessage()));
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $idusuario_a_actualizar       = (int) $_POST['idusuario'];
    $nuevo_nombreusuario    = trim($_POST['nombreusuario']);
    $nuevo_nombrecompleto   = trim($_POST['nombrecompleto']);
    $nuevo_tipousu   = trim($_POST['tipousuario']);

    $tipo_usuario_original_db = '';
    $stmt_check_tipo_original = $enlace->prepare("SELECT tipousuario FROM usuarios WHERE idusuario = ?");
    $stmt_check_tipo_original->bind_param("i", $idusuario_a_actualizar);
    $stmt_check_tipo_original->execute();
    $result_tipo_original = $stmt_check_tipo_original->get_result();
    if ($result_tipo_original->num_rows > 0) {
        $tipo_usuario_original_db = $result_tipo_original->fetch_assoc()['tipousuario'];
    }
    $stmt_check_tipo_original->close();

    $puede_actualizar_datos = false;
    $puede_cambiar_tipo_usuario = false;

    $es_propio_usuario_a_actualizar = ($idusuario_a_actualizar == $id_usuario_sesion);

    if ($es_admin_logueado){
        $puede_actualizar_datos = true;
        if (!$es_propio_usuario_a_actualizar) {
            $puede_cambiar_tipo_usuario = true;
        }
    } elseif ($es_secretario_logueado) {
        if ($es_propio_usuario_a_actualizar) {
            $puede_actualizar_datos = true;
        } elseif ($tipo_usuario_original_db === 'Medico') {
            $puede_actualizar_datos = true;
        } else {
            header("Location: ../main/usuarios.php?error=usuario_modificar_denegado");
            exit;
        }
    } elseif ($es_medico_logueado) {
        if ($es_propio_usuario_a_actualizar) {
            $puede_actualizar_datos = true;
        } else {
            header("Location: ../main/usuarios.php?error=usuario_modificar_denegado");
            exit;
        }
    }
    try {
        if ($es_medico_logueado) {
            $sql_actualizar_medico = "UPDATE usuarios SET nombreusuario = ?, nombrecompleto = ? WHERE idusuario = ?";
            $stmt_actualizar = $enlace->prepare($sql_actualizar_medico);
            if (!$stmt_actualizar) {
                throw new Exception("fallo_consulta_preparar_medico");
            }
            $stmt_actualizar->bind_param("ssi", $nuevo_nombreusuario, $nuevo_nombrecompleto, $idusuario_a_actualizar);
            
            if ($stmt_actualizar->execute()) {
                if ($es_propio_usuario_a_actualizar) {
                    $_SESSION['nombreusuario'] = $nuevo_nombreusuario;
                    $_SESSION['nombrecompleto'] = $nuevo_nombrecompleto;
                }
                header("Location: ../main/index.php?success=usuario_modificado");
                exit; 
            } else {
                throw new Exception("fallo_actualizacion_medico"); 
            } 
        } else {
            if ($puede_cambiar_tipo_usuario) {
                $sql_actualizar = "UPDATE usuarios SET nombreusuario = ?, nombrecompleto = ?, tipousuario = ? WHERE idusuario = ?";
                $stmt_actualizar = $enlace->prepare($sql_actualizar);
                if (!$stmt_actualizar) {
                    throw new Exception("fallo_consulta_preparar");
                }
                $stmt_actualizar->bind_param("sssi", $nuevo_nombreusuario, $nuevo_nombrecompleto, $nuevo_tipousu, $idusuario_a_actualizar);
            } else {
                $sql_actualizar = "UPDATE usuarios SET nombreusuario = ?, nombrecompleto = ? WHERE idusuario = ?";
                if (!$stmt_actualizar) {
                    throw new Exception("fallo_consulta_preparar_sin_tipo");
                }
                $stmt_actualizar->bind_param("ssi", $nuevo_nombreusuario, $nuevo_nombrecompleto, $idusuario_a_actualizar);
            }
        if ($stmt_actualizar->execute()) {
            if ($es_propio_usuario_a_actualizar) {
                $_SESSION['nombreusuario'] = $nuevo_nombreusuario;
                $_SESSION['nombrecompleto'] = $nuevo_nombrecompleto;
                if ($puede_cambiar_tipo_usuario) {
                    $_SESSION['tipousuario'] = $nuevo_tipousu;
                }
            }
            header("Location: ../main/usuarios.php?success=usuario_modificado"); // Redirección para Admin/Secretario
            exit;
        } else {
            throw new Exception("fallo_actualizacion");
        }
    }
    if($stmt_actualizar) {
        $stmt_actualizar->close();
    }
    } catch (Exception $e) {
        error_log("Error al guardar usuario en POST: " . $e->getMessage()); 
        header("Location: ../main/usuarios.php?error=" . urlencode($e->getMessage()));
        exit;
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
        <h1 class="usuario">Modificar Usuario</h1>

        <?php if (!empty($error_interno)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
            <p><a href="../main/usuarios.php" class="button">Volver a la lista de usuarios</a></p>
        <?php elseif (isset($idusuario) && $idusuario): ?>
            <form action="modificar-usuario.php" method="post">
                <input type="hidden" name="idusuario" value="<?= htmlspecialchars($idusuario) ?>">

                <?php if ($es_admin_logueado): ?>
                    <div class="formulario">
                        <label for="nombreusuario">Nombre de Usuario:</label>
                        <input type="text" id="nombreusuario" name="nombreusuario"
                        value="<?= htmlspecialchars($nombreusuario) ?>" class="form-control" required>
                        <label for="nombrecompleto">Nombre Completo:</label>
                        <input type="text" id="nombrecompleto" name="nombrecompleto"
                        value="<?= htmlspecialchars($nombrecompleto) ?>" class="form-control" required>
                        <label for="tipousuario">Tipo de Usuario:</label>
                        <?php if ($es_propio_usuario):   ?>
                            <input type="text" id="tipousuario" name="tipousuario_actual" value="<?php echo $tipousuario; ?>" readonly class="form-control">
                            <small class="text-muted">No puedes cambiar tu tipo de usuario.</small>
                        <?php elseif ($tipousuario === 'Medico' || $tipousuario === 'Secretario'): ?>
                            <select id="tipousuario" name="tipousuario" class="form-control" required>
                                <option value="Medico" <?php if ($tipousuario === 'Medico') echo 'selected'; ?>>Médico</option>
                                <option value="Secretario" <?php if ($tipousuario === 'Secretario') echo 'selected'; ?>>Secretario</option>
                            </select>        
                        <?php endif; ?>
                    </div>
                <?php elseif ($es_secretario_logueado): ?>
                    <div class="formulario">
                    <?php if($es_propio_usuario): ?>
                       <label for="nombreusuario">Nombre de Usuario:</label>
                            <input type="text" id="nombreusuario" name="nombreusuario"
                            value="<?= htmlspecialchars($nombreusuario) ?>" maxlength="255" class="form-control" required>
                        <label for="nombrecompleto">Nombre Completo:</label>
                            <input type="text" id="nombrecompleto" name="nombrecompleto"
                            value="<?= htmlspecialchars($nombrecompleto) ?>" maxlength="255" class="form-control" required>
                        <label for="tipousuario">Tipo de Usuario:</label>
                        <input type="text" id="tipousuario" name="tipousuario" value="<?php echo $tipousuario; ?>" readonly class="form-control">
                        <small class="text-muted">No puedes cambiar tu tipo de usuario.</small>
                        <input type="hidden" name="tipousuario_actual" value="Secretario"> 
                    <?php elseif ($tipousuario === 'Administrador' || ($tipousuario === 'Secretario' && !$es_propio_usuario)): ?> <!-- Esto evita que los Secretarios se modifiquen datos entre ellos. --> <!-- Este Condicional es solo para los Secretarios. -->
                        <label for="nombreusuario">Nombre de Usuario:</label>
                        <input type="text" name="nombreusuario" value="<?php echo htmlspecialchars($nombreusuario); ?>" readonly class="form-control">
                        <small class="text-muted">No puedes modificar datos de otro Secretario o del Administrador.</small>
                        <label for="nombrecompleto">Nombre Completo:</label>
                        <input type="text" name="nombrecompleto" value="<?php echo htmlspecialchars($nombrecompleto); ?>" readonly class="form-control">
                        <small class="text-muted">No puedes modificar datos de otro Secretario o del Administrador.</small>
                        <label for="tipousuario">Tipo de Usuario:</label>
                        <input type="text" id="tipousuario" name="tipousuario" value="<?= htmlspecialchars($tipousuario) ?>" readonly class="form-control">
                        <small class="text-muted">No puedes modificar el tipo de usuario de un Administrador o de otro Secretario.</small>
                        <input type="hidden" name="tipousuario_actual" value="<?= htmlspecialchars($tipousuario) ?>">
                    <?php elseif ($tipousuario === 'Medico'):  ?>
                        <label for="nombreusuario">Nombre de Usuario:</label>
                        <input type="text" id="nombreusuario" name="nombreusuario"
                        value="<?= htmlspecialchars($nombreusuario) ?>" maxlength="255" class="form-control" required>
                        <label for="nombrecompleto">Nombre Completo:</label>
                        <input type="text" id="nombrecompleto" name="nombrecompleto"
                        value="<?= htmlspecialchars($nombrecompleto) ?>" maxlength="255" class="form-control" required>
                        <label for="tipousuario">Tipo de Usuario:</label>
                        <input type="text" id="tipousuario" name="tipousuario" value="<?php echo $tipousuario; ?>" readonly class="form-control">
                        <small class="text-muted">No puedes cambiar el tipo de usuario de un Medico.</small>
                        <input type="hidden" name="tipousuario_actual" value="Medico">
                    <?php endif; ?> 
                    </div> 
                <?php elseif ($es_medico_logueado): ?>
                    <div class="formulario">
                        <?php if ($es_propio_usuario): ?>
                        <label for="nombreusuario">Nombre de Usuario:</label>
                            <input type="text" id="nombreusuario" name="nombreusuario"
                            value="<?= htmlspecialchars($nombreusuario) ?>" maxlength="255" class="form-control" required>
                        <label for="nombrecompleto">Nombre Completo:</label>
                            <input type="text" id="nombrecompleto" name="nombrecompleto"
                            value="<?= htmlspecialchars($nombrecompleto) ?>" maxlength="255" class="form-control" required>
                        <label for="tipousuario">Tipo de Usuario:</label>
                        <input type="text" id="tipousuario" name="tipousuario" value="<?php echo $tipousuario; ?>" readonly class="form-control">
                        <small class="text-muted">No puedes cambiar tu tipo de usuario.</small>
                        <input type="hidden" name="tipousuario_actual" value="Medico">
                        <?php else: ?>
                            <p class="alert alert-danger">Acceso denegado. Un Médico solo puede modificar su propio perfil.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="formulario">
                    <button type="submit" name="guardar">Guardar Cambios</button>
                    <!-- <a href="../main/usuarios.php" class="button">Cancelar</a> -->
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



