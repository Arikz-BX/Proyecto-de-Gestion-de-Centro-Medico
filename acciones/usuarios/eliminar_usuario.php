<?php
session_start();
include('../../conexion/conexionbasededatos.php');

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['idusuario']) && is_numeric($_POST['idusuario'])) {
    $idusuario_eliminar = (int) $_POST['idusuario'];

    mysqli_begin_transaction($enlace);
    try{
    $sql_eliminar_agenda = "DELETE FROM agenda WHERE idmedico IN (SELECT idmedico FROM medicos WHERE idusuario = ?)";
    $stmt_eliminar_agenda = $enlace->prepare($sql_eliminar_agenda);
    $stmt_eliminar_agenda->bind_param("i", $idusuario_eliminar);
    $stmt_eliminar_agenda->execute();
    $stmt_eliminar_agenda->close();

    $sql_eliminar_medicos = "UPDATE medicos SET estado = 'Inactivo' WHERE idusuario = ?";
    $stmt_eliminar_medicos = $enlace->prepare($sql_eliminar_medicos);

    if ($stmt_eliminar_medicos) {
        $stmt_eliminar_medicos->bind_param("i", $idusuario_eliminar);
        $stmt_eliminar_medicos->execute();
        $stmt_eliminar_medicos->close();

        $sql_eliminar_usuario = "UPDATE usuarios SET estado = 'Inactivo' WHERE idusuario = ?";
        $stmt_eliminar_usuario = $enlace->prepare($sql_eliminar_usuario);

        if ($stmt_eliminar_usuario) {
            $stmt_eliminar_usuario->bind_param("i", $idusuario_eliminar);

            if ($stmt_eliminar_usuario->execute()) {
                $stmt_eliminar_usuario->close();
            } else {
                header("Location: ../../main/usuarios.php?error=error_al_eliminar_usuario");
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

    $enlace->close();
    } catch (Exception $e) {
        mysqli_rollback($enlace);
        header("Location: ../../main/usuarios.php?error=error_al_inactivar_usuario&detalle=" . urlencode($e->getMessage()));
        exit();
    } finally {
        $enlace->close();
    }
} else {
    header("Location: ../../main/usuarios.php?error=id_usuario_invalido");
    exit();
}
?>