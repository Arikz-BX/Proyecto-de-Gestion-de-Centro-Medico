<?php
session_start();
include('../../conexion/conexionbasededatos.php');

if(isset($_POST["registrar"])){
    $usuario = mysqli_real_escape_string($enlace, $_POST['usuario']);
    $nombre = mysqli_real_escape_string($enlace, $_POST['nombre']);
    $clave = mysqli_real_escape_string($enlace, $_POST['clave']);
    $tipousuario = mysqli_real_escape_string($enlace, $_POST['tipousuario']);
    $clavehash = password_hash($clave, PASSWORD_DEFAULT);
    $matricula = isset($_POST['matricula']) ? mysqli_real_escape_string($enlace, $_POST['matricula']) : '';
    
    try{
        // Inicia una transacción para asegurar la integridad de los datos
        mysqli_begin_transaction($enlace);

        //Para el Caso del Usuario que ya Existe.
        $stmt_chequear_usuario = $enlace->prepare("SELECT idusuario FROM usuarios WHERE nombreusuario = ?");
        if (!$stmt_chequear_usuario) {
            throw new Exception("Error al preparar la verificacion de usuario: " . $enlace->error);
        }
        $stmt_chequear_usuario->bind_param("s", $usuario);
        $stmt_chequear_usuario->execute();
        $stmt_chequear_usuario->store_result();
        
        if ($stmt_chequear_usuario->num_rows > 0) {
            $stmt_chequear_usuario->close();    
            mysqli_rollback($enlace);
            header("Location: ../../main/registro-sesion.php?error=usuario_ya_existe");
            exit();
        }
        $stmt_chequear_usuario->close();
        
        // Inserta en la tabla de usuarios
        $stmt_registrar_usuario = $enlace->prepare("INSERT INTO usuarios (nombreusuario, usuarioclave, tipousuario, estado) VALUES (?, ?, ?, 'Activo')");
        if (!$stmt_registrar_usuario) {
            throw new Exception("Error al preparar la inserción de usuario: " . $enlace->error); 
        }
        $stmt_registrar_usuario->bind_param("sss", $usuario, $clavehash, $tipousuario);
        
        if (!$stmt_registrar_usuario->execute()) {
            throw new Exception("Error al insertar el usuario: " . $stmt_registrar_usuario->error);
        }
        // Obtiene el ID del usuario recién insertado
        $id_usuario = mysqli_insert_id($enlace);
        $stmt_registrar_usuario->close();
        if ($tipousuario == 'Medico') {
            // Inserta en la tabla de médicos
            $stmt_registrar_medico = $enlace->prepare("INSERT INTO medicos (idusuario, nombrecompleto, matricula) VALUES (?, ?, ?)"); //Se guarda el Nombre con la Matricula, esto para que el Medico ya este cargado en Usuarios y Medicos y solo haya que cargar sus otros datos en Agregar Medico
            
            if (!$stmt_registrar_medico) {
                // Si falla la inserción del médico, revierte la transacción
                mysqli_rollback($enlace);
                echo "<script>
                    alert('Error al registrar los datos del médico: " . $enlace->error . "')
                    window.location = '../../main/registro-sesion.php'
                </script>";
            }
            $stmt_registrar_medico->bind_param("iss", $id_usuario, $nombre, $matricula);    
            if (!$stmt_registrar_medico->execute()) {
                // Si falla la inserción del médico, revierte la transacción
                mysqli_rollback($enlace);
                echo "<script>
                alert('Error al insertar los datos del médico: " . $enlace->error . "')
                window.location = '../../main/registro-sesion.php'
                </script>";
                exit();
            }
            $stmt_registrar_medico->close();
            mysqli_commit($enlace);    
            header("Location: ../../main/agregar-medico.php?success=registro_medico_inicial&idusuario_precargado=" . $id_usuario);
            exit();
        }
            mysqli_commit($enlace);
            header("Location: ../../main/usuarios.php?success=usuario_registrado");
            exit();
    } catch (Exception $e) {
        mysqli_rollback($enlace);
        error_log("Error de Registro". $e->getMessage());
        header("Location: ../../main/registro-sesion.php?error=registro_fallido&detalle=" . urlencode($e->getmessage()));
        exit(); 
    } finally {
        if ($enlace){
            $enlace->close();
        }
    }   
} else {
    echo "<script>
    alert('Error de Registro.')
    window.location = '../../main/registro-sesion.php'
    </script>"; 
}
?>