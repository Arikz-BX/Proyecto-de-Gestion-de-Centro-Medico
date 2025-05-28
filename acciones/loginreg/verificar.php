<?php
session_start();
include('../../conexion/conexionbasededatos.php');
    $usuario=$_POST['usuario'];
    $clave=$_POST['clave'];


    $consulta = "SELECT idusuario, nombreusuario, usuarioclave, tipousuario, estado FROM usuarios WHERE nombreusuario = ? AND estado ='Activo'"; //Abre consulta directo en el SQL
    $stmt = $enlace->prepare($consulta);

    if($stmt) {
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado=$stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        if (password_verify($clave, $fila['usuarioclave'])) {
            session_regenerate_id(true);
            $_SESSION['nombreusuario'] = $fila['nombreusuario']; // Usar el nombre de usuario de la base de datos
            $_SESSION['tipousuario'] = $fila['tipousuario'];
            $_SESSION['idusuario'] = $fila['idusuario'];

            if ($fila['tipousuario'] == 'Medico') {
                $consulta_medico = "SELECT idmedico FROM medicos WHERE idusuario = ?"; 
                $stmt_medico = $enlace->prepare($consulta_medico);
                $stmt_medico->bind_param("i", $fila['idusuario']); 
                $stmt_medico->execute();
                $resultado_medico = $stmt_medico->get_result();

                if ($resultado_medico->num_rows > 0) {
                    $fila_medico = $resultado_medico->fetch_assoc();
                    $_SESSION['idmedico'] = $fila_medico['idmedico']; 
                } 
                $stmt_medico->close(); 
            }
        if ($fila['tipousuario'] == 'Medico') {
            header("Location: ../../main/index.php");  
        } elseif ($fila['tipousuario'] == 'Secretario') {
            header("Location: ../../main/indexsecretario.php");
        } elseif ($fila['tipousuario'] == 'Administrador') {
            header("Location: ../../main/indexadmin.php");
        } else {
          header("Location: ../../main/index.php");
          // Usuario no encontrado
          echo '<div class="error">ERROR DE VERIFICACION: Error de Registro</div>';
          include('../../main/inicio-sesion.php');
            }
            exit();
        } else {
        echo '<div class="error">ERROR: Algo salio mal.'; //Este se acciona Si la Contraseña esta mal colocada.
        include('../../main/inicio-sesion.php');
        exit();
        }
    } elseif ($fila['estado'] == 'Inactivo') {
                echo '<div class="error">ERROR: Esta cuenta está inactiva. Contacte al administrador.</div>';
                include('../../main/inicio-sesion.php');
                exit(); 
    } else {
        echo '<div class="error">ERROR DE VERIFICACION: Usuario no encontrado</div>'; 
        include('../../main/inicio-sesion.php');
        exit();
    } 
    $stmt->close(); 
    } else {
        echo'<div class="error"> ERROR: >' . $enlace->error . '</div>';
        include('../../main/inicio-sesion.php'); 
        exit();
    }  
$enlace->close();
?>