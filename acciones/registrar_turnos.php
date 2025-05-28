<?php
include('../conexion/conexionbasededatos.php');
if (isset($_POST['idmedico'], $_POST['idpaciente'], $_POST['fecha'], $_POST['lugar'])) {
    $idmedico = $_POST['idmedico'];
    $idpaciente = $_POST['idpaciente'];
    $fecha = $_POST['fecha'];
    $lugar = $_POST['lugar'];
    $observacion = $_POST['observacion'];
    $estado = $_POST['estado'] ?? 'Asignado';

    $consulta_medico = "SELECT consultorio, nombrecompleto FROM medicos WHERE idmedico = ?"; //Bindear direccion y nombre al ID para los selectores.
    $stmt_medico = $enlace->prepare($consulta_medico);
    $stmt_medico->bind_param("i", $idmedico);
    $stmt_medico->execute();
    $resultado_medico = $stmt_medico->get_result();

    $consulta_paciente = "SELECT nombrepaciente FROM pacientes WHERE idpaciente = ?";
    $stmt_paciente = $enlace->prepare($consulta_paciente);
    $stmt_paciente->bind_param("i", $idpaciente);
    $stmt_paciente->execute();
    $resultado_paciente = $stmt_paciente->get_result();

    if ($resultado_medico->num_rows > 0 && $resultado_paciente->num_rows > 0) {
        $fila_medico = $resultado_medico->fetch_assoc();
        $consultorio_medico = $fila_medico['consultorio'];
        $nombre_medico = $fila_medico['nombrecompleto'];

        $fila_paciente = $resultado_paciente->fetch_assoc();
        $nombre_paciente = $fila_paciente['nombrepaciente'];

        if ($lugar != $consultorio_medico) {
            echo "<div class='error'>Error: El turno solo se puede asignar en la dirección del médico: " . $consultorio_medico . "</div>";
            exit();
        }

        $consulta = "INSERT INTO turnos (idmedico, idpaciente, nombremedico, nombrepaciente, fecha, lugar, observacion, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
          $stmt = $enlace->prepare($consulta);
          if ($stmt) { $stmt->bind_param("iississs", $idmedico, $idpaciente, $nombre_medico, $nombre_paciente, $fecha, $lugar, $observacion, $estado);
            if ($stmt->execute()) {
                echo "Turno registrado exitosamente.";
                header("Location: ../main/turnos.php");
                exit();
            } else {
                echo "Error al registrar turno: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $enlace->error;
        }
    } else {
        echo "Error: No se encontró la dirección del médico o el paciente.";
        exit();
    }
    $stmt_medico->close();
    $stmt_paciente->close();
} else {
    echo "Error: No se recibieron todos los datos del formulario.";
    exit();
}
?> 
