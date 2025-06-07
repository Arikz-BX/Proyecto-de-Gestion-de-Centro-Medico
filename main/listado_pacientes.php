<?php
session_start();
include('../conexion/conexionbasededatos.php');
// var_dump($_SESSION);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="icon" href="../estilos/medicoslista.ico">
</head>
<body>
    <div class="container">
        <div class= "lista-pacientes">
            <h2>Listado de Pacientes</h2>
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    require_once '../conexion/conexionbasededatos.php';
                    $resultado = obtenerListaDePacientes($enlace);
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                        $idpaciente = htmlspecialchars($row['idpaciente'], ENT_QUOTES, 'UTF-8');
                        $estado_paciente = htmlspecialchars($row['estado'], ENT_QUOTES, 'UTF-8');
                        $clase_fila = '';
                        if ($estado_paciente == 'Activo') {
                            $clase_fila = 'fila-paciente-activo';
                        } elseif ($estado_paciente == 'Inactivo') {
                            $clase_fila = 'fila-paciente-inactivo';
                        }
                        
                        echo '<tr class= "' . $clase_fila . '">';
                        echo "<td>" . $idpaciente . "</td>";
                        echo "<td>" . htmlspecialchars($row['nombrepaciente'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['obrasocial'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['direccion'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['telefono'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['correoelectronico'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['notas'], ENT_QUOTES, "UTF-8") . "</td>";
                        $clase_badge_estado = '';
                        if($estado_paciente == 'En Tratamiento'){
                            $clase_badge_estado = 'text-bg-success';
                        } elseif ($estado_paciente == 'En Espera') {
                            $clase_badge_estado = 'text-bg-danger';
                        } else {
                            $clase_badge_estado = 'text-bg-secondary';
                        }
                        echo '<td><span class="badge '. $clase_badge_estado . '">' . $estado_paciente . '</span></td>';
        
                        echo "<td>";
                        echo "<form action='../main/modificar-paciente.php' method='post' style='display:inline;'>";
                            echo "<input type='hidden' name='idpaciente' value='{$idpaciente}'>";
                            echo "<button type='submit' class='boton-modificar'>Modificar</button>";
                        echo "</form>"; 
                        echo "<form action='../acciones/pacientes/eliminar_paciente.php' method='post' style='display:inline' onsubmit='return confirm(\"¿Estás seguro de que deseas eliminar a este paciente?\")'>";
                            echo "<input type='hidden' name='idpaciente' value='" . $idpaciente. "'>";
                            echo "<button type='submit' class='boton-eliminar'>Eliminar</button>";
                        echo "</form>";
                    echo "</td>";
                echo "</tr>";
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
        </div>    
    </div>
    <?php generarBotonRetorno(); //Para el boton de Retorno que aplique a Secretarios y Administrador.?>
</body>
</html>
