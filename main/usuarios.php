<?php
session_start();
include('../conexion/conexionbasededatos.php');
if (!isset($_SESSION['nombreusuario'])) {
    header("Location: ../main/inicio-sesion.php"); // Redirige a la página de inicio de sesión si no hay sesión
    exit();
}

function obtenerListaDeUsuarios($enlace) {
    $sql = "SELECT idusuario, nombreusuario, tipousuario FROM usuarios";
    $resultado = $enlace->query($sql);
    return $resultado;
}

function obtenerListaDeMedicos($enlace) {
    $sql = "SELECT idmedico, nombrecompleto, idusuario FROM medicos";
    $resultado = $enlace->query($sql);
    return $resultado;
}

function obtenerListaDeSecretarios($enlace) {
    $sql = "SELECT idusuario, nombreusuario, tipousuario FROM usuarios WHERE tipousuario = 'Secretario'";
    $resultado = $enlace->query($sql);
    return $resultado;
}

function generarBotonRetorno() {
    if (isset($_SESSION['tipousuario'])){
        if($_SESSION['tipousuario'] == 'Administrador'){
            echo '<button onclick="window.location.href=\'indexadmin.php\'">Regresar al Inicio.</button>';
        } elseif($_SESSION['tipousuario'] == 'Secretario'){
            echo '<button onclick="window.location.href=\'indexsecretario.php\'">Regresar al Inicio.</button>';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link rel="icon" href="../estilos/usuarios.ico">
</head>
<body>
    <div class="container">
        <h1>Gestión de Usuarios</h1>

        <div class="lista-usuarios">
            <h2>Lista de Usuarios</h2>
            <?php
            $resultado_usuarios = obtenerListaDeUsuarios($enlace);
            if ($resultado_usuarios->num_rows > 0) {
                echo "<table>
                        <thead>
                            <tr>
                                <th>ID Usuario</th>
                                <th>Nombre de Usuario</th>
                                <th>Tipo de Usuario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>" 
            ?>
             <tr>
                <td><?= $id ?></td>
                <td><?= htmlspecialchars($fila_usuario['nombreusuario'], ENT_QUOTES) ?></td>
                <td><?= $tipo ?></td>
                <td><img src="<?= $icon ?>" alt="<?= $tipo ?>" class="dashboard-icon"></td>
                <td>
                <form action="modificar-usuario.php" method="post">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" class="boton-modificar">Modificar</button>
                </form>
                <?php if ($fila_usuario["nombreusuario"] != $_SESSION['nombreusuario']) { //Previene que se borre a si mismo el Secretario/Administrador. ?> 
                <form action="../acciones/usuarios/eliminar_usuario.php"  method="post" style="display:inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este usuario?')">
                    <input type="hidden" name="idusuario" value="<?= $id ?>">
                    <button type="submit">Eliminar</button>
                </form>
                <?php } ?>
                </td>
            </tr>
            <?php
                echo "</tbody>
                    </table>";
            } else {
                echo "<p>No hay usuarios registrados.</p>";
            }
            ?>
        </div>
        <a href="registro-sesion.php">Registrar Nuevo Usuario</a>
    <?php if ($_SESSION['tipousuario'] == 'Administrador') { ?>
        <a href="registro-sesion.php">Registrar Nuevo Secretario</a>
    </div>
    <?php } else { ?>
    <p>No tienes permiso para modificar esta tabla.</p> 
    </div> 
<?php } ?>
<?php generarBotonRetorno(); //Para el boton de Retorno que aplique a Secretarios y Administrador.?>
</body>
</html>