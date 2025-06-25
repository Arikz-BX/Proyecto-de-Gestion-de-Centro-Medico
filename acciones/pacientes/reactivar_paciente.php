<?php
session_start();
include('../../conexion/conexionbasededatos.php');
$idpaciente_a_reactivar       = null;

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['idpaciente']) && is_numeric($_POST['idpaciente'])) {
    $idpaciente_a_reactivar = (int) $_POST['idpaciente'];

    mysqli_begin_transaction($enlace);
    try{
        $sql_dar_alta_paciente = "UPDATE pacientes SET estado = 'En Espera' WHERE idpaciente = ?";
        $stmt_dar_alta_paciente = $enlace->prepare($sql_dar_alta_paciente);

        if ($stmt_dar_alta_paciente) {
            $stmt_dar_alta_paciente->bind_param("i", $idpaciente_a_reactivar);

            if ($stmt_dar_alta_paciente->execute()) {
                $stmt_dar_alta_paciente->close();
            } else {
                header("Location: ../../main/listado_pacientes.php?error=fallo_paciente_reactivado");
                exit();
            }
        } else {
            header("Location: ../../main/listado_pacientes.php?error=error_en_consulta_paciente");
            exit();
        } 
    mysqli_commit($enlace);
        header("Location: ../../main/listado_pacientes.php?success=usuario_reactivado");
        exit();


    } catch (Exception $e) {
        mysqli_rollback($enlace);
        header("Location: ../../main/listado_pacientes.php?error=error_al_inactivar_usuario&detalle=" . urlencode($e->getMessage()));
        exit();
    } finally {
        if($enlace){
        $enlace->close();
        }
    }
} else {
    header("Location: ../../main/listado_pacientes.php?error=id_usuario_invalido");
    exit();
}
?>