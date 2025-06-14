<?php
session_start();
include('../../conexion/conexionbasededatos.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idpaciente']) && ctype_digit($_POST['idpaciente'])) {
    $idpaciente_a_inactivar = (int) $_POST['idpaciente'];
    try {
      $enlace->begin_transaction();

      $sql_chequeo_turnos = "SELECT COUNT(*) FROM turnos WHERE idpaciente = ? AND fecha >= CURDATE()";
      $stmt_chequeo_turnos = $enlace->prepare($sql_chequeo_turnos);

      if (!$stmt_chequeo_turnos) {
        throw new Exception("Error al preparar la consulta de verificacion de turnos: " . $enlace->error);
        
      }
      $stmt_chequeo_turnos->bind_param("i", $idpaciente_a_inactivar);
      $stmt_chequeo_turnos->execute();
      $stmt_chequeo_turnos->bind_result($contador_turnos_activos);
      $stmt_chequeo_turnos->fetch();
      $stmt_chequeo_turnos->close();

      if ($contador_turnos_activos > 0) {
        mysqli_rollback($enlace);
        header("Location: ../../main/listado_pacientes.php?error=paciente_en_tratamiento");
        exit();
      }  

      $sql_inactivar_paciente = ("UPDATE pacientes SET estado = 'Dado de Alta' WHERE idpaciente = ?");
      $stmt_inactivar_paciente = $enlace->prepare($sql_inactivar_paciente);
      if (!$stmt_inactivar_paciente) {
        throw new Exception("Error al preparar la consulta de dar el alta del paciente: " . $enlace->error);
      }
      $stmt_inactivar_paciente->bind_param("i", $idpaciente_a_inactivar);

      if(!$stmt_inactivar_paciente->execute()) {
        throw new Exception("Error al ejecutar el dado de alta del paciente: " . $stmt_inactivar_paciente->error);
      }
      $filas_afectadas = $stmt_inactivar_paciente->affected_rows;
      $stmt_inactivar_paciente->close();
      
      $enlace->commit();

      if ($filas_afectadas > 0) {
        header("Location: ../../main/listado_pacientes.php?success=paciente_dado_de_alta");
      } else {
        header("Location: ../../main/listado_pacientes.php?info=paciente_ya_dado_de_alta");
      }
      exit();
    
    } catch (Exception $e) {
       mysqli_rollback($enlace);
       header("Location: ../../main/listado_pacientes.php?error=error_al_dar_alta_paciente&detalle=" . urlencode($e->getMessage()));
      exit();
    } finally {
      if ($enlace) {
        $enlace->close();
      }
    }

} else {
  header("Location: ../../main/listado_pacientes.php?error=id_invalido");
  exit;
}
?>
