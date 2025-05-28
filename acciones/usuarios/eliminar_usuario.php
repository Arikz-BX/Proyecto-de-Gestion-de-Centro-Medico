<?php
session_start();
include('../../conexion/conexionbasededatos.php');

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['idusuario']) && is_numeric($_POST['idusuario'])) {
    $idusuario_eliminar = (int) $_POST['idusuario'];

    $sql_eliminar_agenda = "DELETE FROM agenda WHERE idmedico IN (SELECT idmedico FROM medicos WHERE idusuario = ?)";
    $stmt_eliminar_agenda = $enlace->prepare($sql_eliminar_agenda);
    $stmt_eliminar_agenda->bind_param("i", $idusuario_eliminar);
    $stmt_eliminar_agenda->execute();
    $stmt_eliminar_agenda->close();

    $sql_eliminar_medicos = "UPDATE medicos SET estado = 'Inactivo'";
    $stmt_eliminar_medicos = $enlace->prepare($sql_eliminar_medicos);

    if ($stmt_eliminar_medicos) {
        $stmt_eliminar_medicos->bind_param("i", $idusuario_eliminar);
        $stmt_eliminar_medicos->execute();
        $stmt_eliminar_medicos->close();

        $sql_eliminar_usuario = "UPDATE usuarios SET estado = 'Inactivo'";
        $stmt_eliminar_usuario = $enlace->prepare($sql_eliminar_usuario);

        if ($stmt_eliminar_usuario) {
            $stmt_eliminar_usuario->bind_param("i", $idusuario_eliminar);

            if ($stmt_eliminar_usuario->execute()) {
                $stmt_eliminar_usuario->close();

                $sql_actualizar_ids = "SET @row_number = 0;";
                $enlace->query($sql_actualizar_ids);

                $sql_reanumerar = "UPDATE usuarios SET idusuario = (@row_number:=@row_number + 1) ORDER BY idusuario ASC;";
                if ($enlace->query($sql_reanumerar)) {
                    header("Location: ../../main/usuarios.php?mensaje=usuario_eliminado_y_ids_renumerados");
                    exit();
                } else {
                    header("Location: ../../main/usuarios.php?error=error_al_renumerar_ids");
                    exit();
                }
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
} else {
    header("Location: ../../main/usuarios.php?error=id_usuario_invalido");
    exit();
}
?>