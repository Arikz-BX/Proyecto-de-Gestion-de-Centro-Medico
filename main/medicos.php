<?php
session_start();
include('../conexion/conexionbasededatos.php'); 
if (!isset($_SESSION['nombreusuario'])) {
    header("Location: ../main/inicio-sesion.php"); // Redirige a la página de inicio de sesión si no hay sesión
    exit();
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
    <title>Listado de Medicos</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="icon" href="../estilos/medicos.ico">
</head>
<body>
    <div class="container">    
        <table>
            <thead>
                <tr>
                    <th>ID Medico</th>
                    <th>Nombre Completo</th>
                    <th>DNI</th>
                    <th>Matricula</th>
                    <th>Consultorio</th>
                    <th>Direccion de Domicilio</th>
                    <th>Telefono</th>
                    <th>Correo</th>
                    <th>Especialidad</th>
                    <th>Estado</th>
                    <th>ID Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                function listMedicos($enlace)
                 {
                    $medicossql = "SELECT * FROM medicos";
                    $resultado = $enlace->query($medicossql);
                    return $resultado;
                }
                try {
                    require_once '../conexion/conexionbasededatos.php';
                    $resultado = listMedicos($enlace);
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                        echo "<tr>
                        <td>{$row['idmedico']}</td>
                        <td>{$row['nombrecompleto']}</td>
                        <td>{$row['dni']}</td>
                        <td>{$row['matricula']}</td>
                        <td>{$row['consultorio']}</td>
                        <td>{$row['direcciondomicilio']}</td>
                        <td>{$row['telefono']}</td>
                        <td>{$row['correo']}</td>
                        <td>{$row['especialidad']}</td>
                        <td>{$row['estado']}</td>
                        <td>{$row['idusuario']}</td>
                        <td>
                        <form action='modificar-medico.php' method='post'>
                            <input type='hidden' name='idmedico' value='{$row['idmedico']}'>
                            <button type='submit' class='boton-modificar'>Modificar</button>
                        </form> 
                        <form action='../acciones/pacientes/eliminar_paciente.php' method='post' style='display:inline'
                            onsubmit='return confirm(\"¿Estás seguro de que deseas eliminar a este Medico?\")'>
                            <input type='hidden' name='idmedico' value='{$row['idmedico']}'>
                            <button type='submit'>Eliminar</button>
                        </form>
                    </td>
                    </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No hay medicos registrados.</td></tr>";
                }
            } catch (Exception $ex){
                echo "<tr><td coldspan='8'>Error: " . $ex->getMessage() . "</td></tr>";   
            }
            ?>   
        </tbody>
    </table>
    <a href="../main/agregar-medico.php" id="agregar">
    <button href="../main/agregar-medico.php" id="agregar" type="submit" class="button">Agregar Médico</button>
    </a>
    <?php generarBotonRetorno(); //Para el boton de Retorno que aplique a Secretarios y Administrador.?>
</body>
</html>