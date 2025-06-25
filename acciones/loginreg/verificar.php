<?php
session_start();
include('../../conexion/conexionbasededatos.php');
    $usuario=$_POST['usuario'];
    $clave=$_POST['clave'];


    $consulta = "SELECT idusuario, nombreusuario, usuarioclave, tipousuario, estado FROM usuarios WHERE nombreusuario = ?"; //Abre consulta directo en el SQL
    $stmt = $enlace->prepare($consulta);

    if($stmt) {
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado=$stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        if ($fila['estado'] == 'Inactivo') {
            echo '<div class="error">ERROR: Esta cuenta está inactiva. Contacte al administrador.</div>';
                header('Location: ../../main/inicio-sesion.php?error=usuario_inactivo');
                exit();
        }

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
                // Usuario no encontrado
                header('Location: ../../main/inicio-sesion.php?error=tipo_usuario_desconocido'); //Esto mostraba mal el formulario jaja
            }
            exit();
        } else {
        //Este se acciona Si la Contraseña esta mal colocada.
        header('Location: ../../main/inicio-sesion.php?error=clave_incorrecta');
        exit();
        }
    } else { 
        header('Location: ../../main/inicio-sesion.php?error=usuario_no_encontrado');
        exit();
    } 
    $stmt->close(); 
    } else {
        header('Location: ../../main/inicio-sesion.php?error=error_consulta_db'); 
        exit();
    }  
$enlace->close();
?>