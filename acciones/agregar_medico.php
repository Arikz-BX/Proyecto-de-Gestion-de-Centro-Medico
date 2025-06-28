<?php
session_start();
include('../conexion/conexionbasededatos.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idusuario = (int) $_POST['idusuario_seleccionado'];
    $nombrecompleto = mysqli_real_escape_string($enlace, $_POST['nombrecompleto']);
    $dni = mysqli_real_escape_string($enlace, $_POST['dni']);
    $consultorio = mysqli_real_escape_string($enlace, $_POST['consultorio']);
    $direccion = mysqli_real_escape_string($enlace, $_POST['direcciondomicilio']);
    $telefono = mysqli_real_escape_string($enlace, $_POST['telefono']);
    $correo = mysqli_real_escape_string($enlace, $_POST['correo']);
    $especialidad = mysqli_real_escape_string($enlace, $_POST['especialidad']);

    if (empty($dni) || empty($consultorio) || empty($direccion) || empty($telefono) || empty($correo) || empty($especialidad)) {
        header("Location: ../../main/agregar-medico.php?error=campos_vacios&idusuario_precargado=" . $idusuario);
        exit();
    }
    
    mysqli_begin_transaction($enlace);
    try{
        $stmt_usuario = $enlace->prepare("SELECT nombrecompleto FROM usuarios WHERE idusuario = ?"); //Consigue el Nombre Completo mediante el ID Usuario
        if (!$stmt_usuario) {
            throw new Exception("Error al preparar la consulta de datos de usuario: " . $enlace->error);
        }
        $stmt_usuario->bind_param("i", $idusuario);
        $stmt_usuario->execute();
        $resultado_usuario = $stmt_usuario->get_result();
        $datos_usuario = $resultado_usuario->fetch_assoc();
        $nombrecompleto_sincronizado = $datos_usuario['nombrecompleto'];
        $stmt_usuario->close();
        
        
        $stmt_check_medico = $enlace->prepare("SELECT idmedico FROM medicos WHERE idusuario = ?");
        if (!$stmt_check_medico) {
            throw new Exception("Error al preparar la verificación de médico: " . $enlace->error);
        }
        $stmt_check_medico->bind_param("i", $idusuario);
        $stmt_check_medico->execute();
        $stmt_check_medico->store_result();
       
        if ($stmt_check_medico->num_rows > 0) {
            // El registro del médico ya existe, ¡actualizar!
            $stmt_check_medico->close();

            $sql_update_medico = "UPDATE medicos 
                                  SET nombrecompleto = ?, dni = ?, consultorio = ?, direcciondomicilio = ?, telefono = ?, correo = ?, especialidad = ?, estado = 'Activo'
                                  WHERE idusuario = ?";
            $stmt_update_medico = $enlace->prepare($sql_update_medico);
            if (!$stmt_update_medico) {
                throw new Exception("Error al preparar la actualización de médico: " . $enlace->error);
            }
            $stmt_update_medico->bind_param("sssssssi", $nombrecompleto_sincronizado, $dni, $consultorio, $direccion, $telefono, $correo, $especialidad, $idusuario);
            $stmt_update_medico->execute();
            $stmt_update_medico->close();
        } else {
            $fila_usuario = $datos_usuario;
            $sql_registrar_medico = ("INSERT INTO medicos (idusuario, nombrecompleto, dni, consultorio, direcciondomicilio, telefono, correo, especialidad, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 'Activo')");   
            $stmt_registrar_medico = $enlace->prepare($sql_registrar_medico);
            if (!$stmt_registrar_medico) {
                throw new Exception("Error en INSERT: " . $enlace->error);
            }
            $stmt_registrar_medico->bind_param("isssssss", $idusuario, $nombrecompleto_sincronizado, $dni, $consultorio, $direccion, $telefono, $correo, $especialidad);
            $stmt_registrar_medico->execute();
            $stmt_registrar_medico->close();
        }
    mysqli_commit($enlace);
    echo "<script>
            alert('Médico agregado correctamente.');
            window.location = '../main/medicos.php?success=medico_registrado';
        </script>";  
    } catch (Exception $e) {
        mysqli_rollback($enlace);
            error_log("Error en agregar_medico.php (accion): " . $e->getMessage());
            header("Location: ../main/agregar-medico.php?error=excepcion&idusuario_precargado=" . $idusuario);
            exit();
    } finally {
        if ($enlace) {
            $enlace->close();
        }
    }
} else {
    echo "<script>
    alert('Acceso no permitido.');
    window.location = '../main/medicos.php';
    </script>";
}
?>