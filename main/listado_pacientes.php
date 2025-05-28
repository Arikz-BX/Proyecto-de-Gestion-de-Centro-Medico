<?php
session_start();
include('../conexion/conexionbasededatos.php');
var_dump($_SESSION);
function obtenerListaDePacientes($enlace) {
    $sql = "SELECT * FROM pacientes";
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
    <title>Listado de Pacientes</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link rel="icon" href="../estilos/medicoslista.ico">
</head>
<body>
    <div class="container">    
        <table>
            <thead>
                <tr>
                    <th>ID Paciente</th>
                    <th>Nombre Completo</th>
                    <th>DNI</th>
                    <th>Obra Social</th>
                    <th>Direccion</th>
                    <th>Telefono</th>
                    <th>Correo</th>
                    <th>Notas</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    require_once '../conexion/conexionbasededatos.php';
                    $resultado = obtenerListaDePacientes($enlace);
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                        $idpaciente = $row['idpaciente'];
                        echo "<tr>
                        <td>{$row['idpaciente']}</td>
                        <td>{$row['nombrepaciente']}</td>
                        <td>{$row['dni']}</td>
                        <td>{$row['obrasocial']}</td>
                        <td>{$row['direccion']}</td>
                        <td>{$row['telefono']}</td>
                        <td>{$row['correoelectronico']}</td>
                        <td>{$row['notas']}</td>
                        <td>{$row['estado']}</td>
                        <td>
                        <form action='../main/modificar-paciente.php' method='post' style='display:inline;'>
                            <input type='hidden' name='idpaciente' value='{$idpaciente}'>
                            <button type='submit' class='boton-modificar'>Modificar</button>
                        </form> 
                        <form action='../acciones/pacientes/eliminar_paciente.php' method='post' style='display:inline'
                            onsubmit='return confirm(\"¿Estás seguro de que deseas eliminar a este paciente?\")'>
                            <input type='hidden' name='idpaciente' value='{$idpaciente}'>
                            <button type='submit'>Eliminar</button>
                        </form>
                    </td>
                </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No hay pacientes registrados.</td></tr>";
                }
            } catch (Exception $ex){
                echo "<tr><td coldspan='8'>Error: " . $ex->getMessage() . "</td></tr>";   
            }
            ?>   
        </tbody>
    </table>
    <a href="../main/pacientes.php" class="button">
    <button type="submit" class="button">Agregar Paciente</button>
    </a>
    <?php generarBotonRetorno(); //Para el boton de Retorno que aplique a Secretarios y Administrador.?>
</body>
</html>
