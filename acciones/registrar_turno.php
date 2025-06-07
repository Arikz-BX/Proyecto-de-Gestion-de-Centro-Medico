<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
include('../conexion/conexionbasededatos.php');
include('../funciones/funcionesturnos.php');

if (isset($_POST['idmedico'], $_POST['idpaciente'], $_POST['fecha'], $_POST['lugar'])) {
    $idmedico = $_POST['idmedico'];
    $idpaciente = $_POST['idpaciente'];
    $fecha_str = $_POST['fecha'];
    $lugar = $_POST['lugar'];
    $observacion = $_POST['observacion'] ?? '';
    $estado = $_POST['estado'] ?? 'Asignado';

    $consulta_medico = "SELECT consultorio, nombrecompleto FROM medicos WHERE idmedico = ? AND estado = 'Activo'"; //Bindear direeccion y nombre al ID para los selectores
    $stmt_medico = $enlace->prepare($consulta_medico);
    $stmt_medico->bind_param("i", $idmedico);
    $stmt_medico->execute();
    $resultado_medico = $stmt_medico->get_result();

    $consulta_paciente = "SELECT nombrepaciente FROM pacientes WHERE idpaciente = ? AND estado = 'En Espera'"; //Se toma en cuenta a los Pacientes que estan En Espera de ser asignados.
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
        }
        try {
            $fecha_turno = new DateTime($fecha_str, new DateTimeZone('America/Argentina/Buenos_Aires'));
            $fecha_actual = new DateTime(null, new DateTimeZone('America/Argentina/Buenos_Aires'));
        } catch (Exception $e) {
            echo "<div class='error'>Error: Formato de fecha incorrecto.</div>";
        }
				// Validar que la fecha del turno no sea en el pasado
        if ($fecha_turno < $fecha_actual) {
            echo "Error: La fecha del turno debe ser de hoy en adelante.";
        }

        // Validar si el turno está dentro del horario de agenda del médico
        $turno_en_agenda = false;
        $consulta_agenda = "SELECT hora_inicio, hora_final, fechalaboral FROM agenda WHERE idmedico = ? AND fechalaboral = ?";
        $stmt_agenda = $enlace->prepare($consulta_agenda);
        $stmt_agenda->bind_param("is", $idmedico, $fecha_turno->format('Y-m-d')); // Usamos el formato Y-m-d para la fecha
        $stmt_agenda->execute();
        $resultado_agenda = $stmt_agenda->get_result();

        if ($resultado_agenda->num_rows > 0) {
            while ($fila_agenda = $resultado_agenda->fetch_assoc()) {
                $hora_inicio_agenda_str = $fila_agenda['hora_inicio']; // HH:MM
                $hora_fin_agenda_str = $fila_agenda['hora_final'];     // HH:MM
                $fecha_laboral_str = $fila_agenda['fechalaboral'];    // YYYY-MM-DD

                $inicio_jornada_medico = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_laboral_str . ' ' . $hora_inicio_agenda_str);
                $fin_jornada_medico = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_laboral_str . ' ' . $hora_fin_agenda_str);


                if ($fecha_turno >= $inicio_jornada_medico && $fecha_turno <= $fin_jornada_medico) {
                    $turno_en_agenda = true;
                    break;
                }
            }
        }
        $stmt_agenda->close();

        if (!$turno_en_agenda) {
             header('Location: ../../main/registro-turnos.php?error=turno_fuera_agenda'); //Cambiada la logica de como mostrar los Errores.
        }
        $consulta = "INSERT INTO turnos (idmedico, idpaciente, nombrepaciente, nombremedico, fecha, lugar, observacion, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
          $stmt = $enlace->prepare($consulta);
          if ($stmt) {
            $fecha_insertar = $fecha_turno->format('Y-m-d H:i:s');
            $stmt->bind_param("iissssss", $idmedico, $idpaciente, $nombre_paciente, $nombre_medico, $fecha_insertar, $lugar, $observacion, $estado);
            if ($stmt->execute()) {
                echo "Turno registrado exitosamente.";
                header("Location: ../main/turnos.php");
                exit();
            } else {
                header ('Location: ../../main/registro-turnos.php?error=fallo_de_registro'); //Cambiada la logica de como mostrar los Errores.
            }

            $stmt->close();
        } else {
            header ('Location: ../../main/registro-turnos.php?error=error_consulta_db'); //Cambiada la logica de como mostrar los Errores.
        }
    } else {
        header ('Location: ../../main/registro-turnos.php?error=consultorio_no_encontrado'); //Cambiada la logica de como mostrar los Errores.
    }
    $stmt_medico->close();
    $stmt_paciente->close();
} else {
    header ('Location: ../../main/registro-turnos.php?error=datos_faltantes_formulario_'); //Cambiada la logica de como mostrar los Errores.
}
?> 