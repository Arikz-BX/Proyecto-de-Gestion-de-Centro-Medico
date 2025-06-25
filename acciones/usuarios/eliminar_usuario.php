<?php
session_start();
include('../../conexion/conexionbasededatos.php');
$idusuario_a_desactivar       = null;

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['idusuario']) && is_numeric($_POST['idusuario'])) {
    $idusuario_a_desactivar = (int) $_POST['idusuario'];

    mysqli_begin_transaction($enlace);
    try{
        $sql_eliminar_agenda = "DELETE FROM agenda WHERE idmedico IN (SELECT idmedico FROM medicos WHERE idusuario = ?)";
        $stmt_eliminar_agenda = $enlace->prepare($sql_eliminar_agenda);
        if (!$stmt_eliminar_agenda) {
            throw new Exception ("Error al preparar la consulta de agenda" . $enlace->error);
        }
        $stmt_eliminar_agenda->bind_param("i", $idusuario_a_desactivar);
        $stmt_eliminar_agenda->execute();
        $stmt_eliminar_agenda->close();

        $sql_dar_baja_medicos = "UPDATE medicos SET estado = 'Inactivo' WHERE idusuario = ?";
        $stmt_dar_baja_medicos = $enlace->prepare($sql_dar_baja_medicos);

        if ($stmt_dar_baja_medicos) {
        $stmt_dar_baja_medicos->bind_param("i", $idusuario_a_desactivar);
        $stmt_dar_baja_medicos->execute();
        $stmt_dar_baja_medicos->close();

        $sql_dar_baja_usuario = "UPDATE usuarios SET estado = 'Inactivo' WHERE idusuario = ?";
        $stmt_dar_baja_usuario = $enlace->prepare($sql_dar_baja_usuario);

        if ($stmt_dar_baja_usuario) {
            $stmt_dar_baja_usuario->bind_param("i", $idusuario_a_desactivar);

            if ($stmt_dar_baja_usuario->execute()) {
                $stmt_dar_baja_usuario->close();
            } else {
                header("Location: ../../main/usuarios.php?error=fallo_usuario_inactivado");
                exit();
            }
        } else {
            header("Location: ../../main/usuarios.php?error=error_en_consulta_usuario");
            exit();
        }
    } else {
        header("Location: ../../main/usuarios.php?error=error_en_consulta_medicos");
        exit();
    }
    mysqli_commit($enlace);
        header("Location: ../../main/usuarios.php?success=usuario_inactivado");
        exit();


    } catch (Exception $e) {
        mysqli_rollback($enlace);
        header("Location: ../../main/usuarios.php?error=error_al_inactivar_usuario&detalle=" . urlencode($e->getMessage()));
        exit();
    } finally {
        if($enlace){
        $enlace->close();
        }
    }
} else {
    header("Location: ../../main/usuarios.php?error=id_usuario_invalido");
    exit();
}
?>