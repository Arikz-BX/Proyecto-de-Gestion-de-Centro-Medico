<?php
session_start();
include('../../conexion/conexionbasededatos.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idmedico']) && ctype_digit($_POST['idmedico'])) {
  $idmedico_a_desactivar = (int) $_POST['idmedico'];

    $stmt_medico = $enlace->prepare("SELECT idusuario FROM medicos WHERE idmedico = ?");
    $stmt_medico->bind_param("i", $idmedico_a_desactivar);
    $stmt_medico->execute();
    $resultado_medico = $stmt_medico->get_result();
    
    if ($resultado_medico->num_rows === 0) {
            throw new Exception("Médico no encontrado.");
        }
    
    $fila_idusuario = $resultado_medico->fetch_assoc();
    $idusuario_asociado = $fila_idusuario['idusuario'];
    $stmt_medico->close();
        /*  if ($resultado_medico->num_rows > 0) {*/
        /*$fila_medico = $resultado_medico->fetch_assoc();*/

        /*$pre_agenda = $enlace->prepare("DELETE FROM agenda WHERE idmedico = ?"); //Elimina las Agendas que Registrara el Medico.
        $pre_agenda->bind_param("i", $idmedico);
        $pre_agenda->execute();
        $pre_agenda->close();*/

        /*$prt_turnos = $enlace->prepare("UPDATE FROM turnos SET estado 'Guardado' WHERE idmedico = ?"); //Elimina los Turnos registrados del Medico.
        $prt_turnos->bind_param("i", $idmedico);
        $prt_turnos->execute();
        $prt_turnos->close();}*/
        /*  $stmt_medico->close();*/
    $pmd_medico = $enlace->prepare("UPDATE medicos SET estado = 'Inactivo' WHERE idmedico = ?");
    $pmd_medico->bind_param("i", $idmedico_a_desactivar);
    if (!$pmd_medico->execute()) {
    header("Location: ../../main/medicos.php?error=error_al_dar_de_baja");
      exit;
    }
    $pmd_medico->close();

    $pmd_usuario = $enlace->prepare("UPDATE usuarios SET estado = 'Inactivo' WHERE idusuario = ?");
    $pmd_usuario->bind_param("i", $idusuario_asociado);
    if (!$pmd_usuario->execute()) {
    header("Location: ../../main/usuarios.php?error=fallo_usuario_inactivado");
      exit;
    }
    $pmd_usuario->close();
    
    header("Location: ../../main/medicos.php?success=medico_inactivado");
    exit;
}