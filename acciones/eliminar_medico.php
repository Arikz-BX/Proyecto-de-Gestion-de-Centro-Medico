<?php
session_start();
include('../../conexion/conexionbasededatos.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idusuario']) && ctype_digit($_POST['idusuario'])) {
    $idusuario = (int) $_POST['idusuario'];

    $stmt_medico = $enlace->prepare("SELECT idmedico FROM medicos WHERE idusuario = ?");
    $stmt_medico->bind_param("i", $idusuario);
    $stmt_medico->execute();
    $resultado_medico = $stmt_medico->get_result();

    if ($resultado_medico->num_rows > 0) {
        $fila_medico = $resultado_medico->fetch_assoc();
        $idmedico = $fila_medico['idmedico'];

        $pre_agenda = $enlace->prepare("DELETE FROM agenda WHERE idmedico = ?"); //Elimina las Agendas que Registrara el Medico.
        $pre_agenda->bind_param("i", $idmedico);
        $pre_agenda->execute();
        $pre_agenda->close();

        $prt_turnos = $enlace->prepare("DELETE FROM turnos WHERE idmedico = ?"); //Elimina los Turnos registrados del Medico.
        $prt_turnos->bind_param("i", $idmedico);
        $prt_turnos->execute();
        $prt_turnos->close();
    }
    $stmt_medico->close();

    $pmd_usuario = $enlace->prepare("UPDATE usuarios SET estado = 'Inactivo' WHERE idusuario = ?");
    $pmd_usuario->bind_param("i", $idusuario);
    if (!$pmd_usuario->execute()) {
        header("Location: ../../main/usuarios.php?mensaje=error_al_dar_de_baja");
        exit;
    }
    $pmd_usuario->close();
    header("Location: ../../main/usuarios.php?mensaje=usuario_dado_de_baja");
    exit;
  $enlace->query("SET @new_id=0;");
  $enlace->query("
    CREATE TEMPORARY TABLE tmp_med AS
    SELECT idmedico AS old_id,
           (@new_id:=@new_id+1)     AS new_id,
           nombrecompleto,
           idusuario
    FROM medicos
    ORDER BY old_id;
  ");

  $enlace->query("
    UPDATE agenda  a 
    JOIN tmp_med m ON a.idmedico = m.old_id
    SET a.idmedico = m.new_id
  ");
  $enlace->query("
    UPDATE turnos  t 
    JOIN tmp_med m ON t.idmedico = m.old_id
    SET t.idmedico = m.new_id
  ");

  $enlace->query("TRUNCATE TABLE medicos;");
  $enlace->query("
    INSERT INTO medicos (idmedico,nombrecompleto,idusuario)
    SELECT new_id, nombrecompleto, idusuario
    FROM tmp_med
    ORDER BY new_id;
  ");
  $enlace->query("
    ALTER TABLE medicos 
      AUTO_INCREMENT = (SELECT MAX(idmedico)+1 FROM medicos);
  ");

  $enlace->query("DROP TEMPORARY TABLE tmp_med;");

  header("Location: ../../main/medicos.php?mensaje=medico_eliminado_y_ids_renumerados");
  exit;
}
header("Location: ../../main/medicos.php?error=id_invalido");
exit;
?>
