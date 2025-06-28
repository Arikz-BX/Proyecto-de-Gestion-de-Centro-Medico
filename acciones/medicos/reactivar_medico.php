<?php
session_start();
include('../../conexion/conexionbasededatos.php');
$idmedico_a_reactivar       = null;

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['idmedico']) && is_numeric($_POST['idmedico'])) {
    $idmedico_a_reactivar = (int) $_POST['idmedico'];
    $idusuario_a_reactivar = (int) $_POST['idusuario'];

    mysqli_begin_transaction($enlace);
    try{

        $sql_dar_alta_medicos = "UPDATE medicos SET estado = 'Activo' WHERE idmedico = ?";
        $stmt_dar_alta_medicos = $enlace->prepare($sql_dar_alta_medicos);

        if ($stmt_dar_alta_medicos) {
        $stmt_dar_alta_medicos->bind_param("i", $idmedico_a_reactivar);
        $stmt_dar_alta_medicos->execute();
        $stmt_dar_alta_medicos->close();

        $sql_dar_alta_usuario = "UPDATE usuarios SET estado = 'Activo' WHERE idusuario = ?";
        $stmt_dar_alta_usuario = $enlace->prepare($sql_dar_alta_usuario);

        if ($stmt_dar_alta_usuario) {
            $stmt_dar_alta_usuario->bind_param("i", $idusuario_a_reactivar);

            if ($stmt_dar_alta_usuario->execute()) {
                $stmt_dar_alta_usuario->close();
            } else {
                header("Location: ../../main/medicos.php?error=fallo_usuario_reactivado");
                exit();
            }
        } else {
            header("Location: ../../main/medicos.php?error=error_en_consulta_usuario");
            exit();
        }
    } else {
        header("Location: ../../main/medicos.php?error=error_en_consulta_medicos");
        exit();
    }
    mysqli_commit($enlace);
        header("Location: ../../main/medicos.php?success=usuario_reactivado");
        exit();


    } catch (Exception $e) {
        mysqli_rollback($enlace);
        header("Location: ../../main/medicos.php?error=error_al_inactivar_usuario&detalle=" . urlencode($e->getMessage()));
        exit();
    } finally {
        if($enlace){
        $enlace->close();
        }
    }
} else {
    header("Location: ../../main/medicos.php?error=id_usuario_invalido");
    exit();
}
?>