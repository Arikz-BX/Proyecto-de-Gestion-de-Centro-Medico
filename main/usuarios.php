<?php
session_start();
include('../conexion/conexionbasededatos.php');
if (!isset($_SESSION['nombreusuario'])) {
    header("Location: ../main/inicio-sesion.php"); // Redirige a la página de inicio de sesión si no hay sesión
    exit();
}

function obtenerListaDeUsuarios($enlace) {
    $sql = "SELECT idusuario, nombreusuario, nombrecompleto, tipousuario FROM usuarios";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
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
                                <th>Nombre completo</th>
                                <th>Tipo de Usuario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>";
                        while ($fila_usuario = $resultado_usuarios->fetch_assoc()) {
                            // Sanitiza los datos para evitar XSS al mostrarlos en HTML
                        $id        = htmlspecialchars($fila_usuario['idusuario'], ENT_QUOTES, 'UTF-8');
                        $nombre_usuario    = htmlspecialchars($fila_usuario['nombreusuario'], ENT_QUOTES, 'UTF-8');
                        $nombre_personal    = htmlspecialchars($fila_usuario['nombrecompleto'], ENT_QUOTES, 'UTF-8');
                        $tipo     = htmlspecialchars($fila_usuario['tipousuario'], ENT_QUOTES, 'UTF-8');
                        echo "<tr>";
                echo "<td>". $id ."</td>"; //Celda del ID en los Usuarios
                echo "<td>". $nombre_usuario . "</td>"; //Celda del Nombre de Usuario
                echo "<td>". $nombre_personal . "</td>"; //Celda del Nombre de Usuario
                echo "<td>". $tipo ."</td>"; //Celda del tipousuario
                echo "<td>"; //Celda de Acciones
                echo "<form action='modificar-usuario.php' method='post' style='display:inline-block; margin-right: 5px;'>";
                echo "<input type='hidden' name='id' value='". $id ."'>";
                echo "<button type='submit' class='boton-modificar'>Modificar</button>";
                echo "</form>";
                if ($fila_usuario["nombreusuario"] != $_SESSION['nombreusuario']) { //Previene que se borre a si mismo el Secretario/Administrador.
                echo "<form action='../acciones/usuarios/eliminar_usuario.php'  method='post' style='display:inline' onsubmit='return confirm(\"¿Estás seguro de que deseas eliminar a este usuario?\")'>";
                echo "<input type='hidden' name='idusuario' value=". $id ."'>";
                echo "<button type='submit'>Eliminar</button>";
                echo "</form>";
                }
                echo "</td>";
                echo "</tr>";
            }
            
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