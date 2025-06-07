<?php
session_start();
include('../../conexion/conexionbasededatos.php');

if(isset($_POST["registrar"])){
    $usuario = mysqli_real_escape_string($enlace, $_POST['usuario']);
    $nombre = mysqli_real_escape_string($enlace, $_POST['nombre']);
    $clave = mysqli_real_escape_string($enlace, $_POST['clave']);
    $tipousuario = mysqli_real_escape_string($enlace, $_POST['tipousuario']);
    $clavehash = password_hash($clave, PASSWORD_DEFAULT);
    $matricula = isset($_POST['matricula']) ? $_POST['matricula'] : '';
    //Para el Caso del Usuario que ya Existe.
    $sqluser = "SELECT idusuario, usuarioclave, tipousuario FROM usuarios WHERE nombreusuario = '$usuario'";
    $resultadousuario = $enlace->query($sqluser);
    
    if ($resultadousuario->num_rows > 0){
        $fila_usuario = $resultadousuario->fetch_assoc();
        //Para verificar la contraseña hasheada vs la ingresada.
        if(password_verify($clave, $fila_usuario['usuarioclave'])) {
            echo "<script>
            alert('El Usuario ya Existe.')
            window.location = '../../main/inicio-sesion.php'
        </script>";
        } else {
            echo "<script>
            alert('El Usuario ya Existe.')
            window.location = '../../main/registro-sesion.php'
        </script>";
        }
    } else {
        // Inicia una transacción para asegurar la integridad de los datos
        mysqli_begin_transaction($enlace);

        // Inserta en la tabla de usuarios
        $sqlusuario = "INSERT INTO usuarios (nombreusuario, usuarioclave, tipousuario) VALUES ('$usuario', '$clavehash', '$tipousuario')";
        $resultado_usuario = $enlace->query($sqlusuario);

        if ($resultado_usuario) {
            // Obtiene el ID del usuario recién insertado
            $id_usuario = mysqli_insert_id($enlace);

            if ($tipousuario == 'Medico') {
                // Inserta en la tabla de médicos
                $sqlmedico = "INSERT INTO medicos (idusuario, nombrecompleto, matricula) VALUES ('$id_usuario','$nombre', '$matricula')"; //Se guarda el Nombre con la Matricula, esto para que el Medico ya este cargado en Usuarios y Medicos y solo haya que cargar sus otros datos en Agregar Medico
                $resultado_medico = $enlace->query($sqlmedico);

                if (!$resultado_medico) {
                    // Si falla la inserción del médico, revierte la transacción
                    mysqli_rollback($enlace);
                    echo "<script>
                        alert('Error al registrar los datos del médico: " . $enlace->error . "')
                        window.location = '../../main/registro-sesion.php'
                    </script>";
                    exit();
                }
            }
            mysqli_commit($enlace);
            echo "<script>
                alert('Registro Exitoso.')
                window.location = '../../main/usuarios.php'
            </script>";
        }else{
            mysqli_rollback($enlace);
            echo "<script>
            alert('Error de Registro.')
            window.location = '../../main/registro-sesion.php'
        </script>"; 
        }
    }
 
}
?>