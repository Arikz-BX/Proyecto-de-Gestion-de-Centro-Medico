<?php
function obtenerMedicos($enlace){
    $medicos = array();
    $consulta = "SELECT idmedico, nombrecompleto, consultorio FROM medicos";
    $resultado = $enlace->query($consulta);
    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $medicos[] = $fila;
        }
    }
    return $medicos;
}

function obtenerPacientes($enlace){
    $pacientes = array();
    $consulta = "SELECT idpaciente, nombrepaciente FROM pacientes";
    $resultado = $enlace->query($consulta);
    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $pacientes[] = $fila;
        }
    }
    return $pacientes;
}
function obtenerListaDeTurnos($enlace) {
    $sql = "SELECT idturno, idmedico, idpaciente, nombrepaciente, nombremedico, fecha, lugar, observacion, estado FROM turnos";
    $resultado = $enlace->query($sql);
    return $resultado;
}


function obtenerAgendaDeMedicos($enlace) {
    $sql = "SELECT medicos.nombrecompleto, medicos.consultorio, agenda.fechalaboral, agenda.hora_inicio, agenda.hora_final 
            FROM agenda 
            INNER JOIN medicos ON agenda.idmedico = medicos.idmedico
            WHERE agenda.fechalaboral >= CURDATE() AND (agenda.fechalaboral > CURDATE() OR agenda.hora_final >= CURTIME())
            ORDER BY agenda.fechalaboral, agenda.hora_inicio";
    $resultado = $enlace->query($sql);
    return $resultado;
}

function obtenerListaDeTurnosIndividual($enlace) {
    $idMedico = $_SESSION['idusuario'];
    $sql = "SELECT turnos.idturno, turnos.idmedico, turnos.idpaciente, turnos.nombrepaciente, turnos.nombremedico, turnos.fecha, turnos.lugar, turnos.observacion, turnos.estado FROM turnos 
            INNER JOIN medicos ON turnos.idmedico = medicos.idmedico
            WHERE medicos.idusuario = $idMedico";
    $resultado = $enlace->query($sql);
    return $resultado;
}

function obtenerAgendaDeMedicosIndividualTurno($enlace) {
    $idmedico = $_POST['idmedico'];
    $sql = "SELECT medicos.nombrecompleto, medicos.consultorio, agenda.fechalaboral, agenda.hora_inicio, agenda.hora_final 
            FROM agenda 
            INNER JOIN medicos ON agenda.idmedico = medicos.idmedico
            WHERE agenda.fechalaboral >= CURDATE() AND (agenda.fechalaboral > CURDATE() OR agenda.hora_final >= CURTIME() AND agenda.idmedico = $idmedico)
            ORDER BY agenda.fechalaboral, agenda.hora_inicio";
    $resultado = $enlace->query($sql);
    return $resultado;
}

function cancelarTurno($enlace, $idturno, $idmedico)
{
    $stmt = $enlace->prepare("UPDATE turnos SET estado = 'Cancelado' WHERE idturno = ? AND idmedico = ?");
    $stmt->bind_param("ii", $idturno, $idmedico);
    $stmt->execute();
    $stmt->close();

    $mensaje = "El médico con ID " . $idmedico . " ha cancelado el turno con ID " . $idturno . ".";
    $stmt = $enlace->prepare("INSERT INTO notificaciones (idturno, tipo, mensaje) VALUES (?, 'Cancelacion', ?)");
    $stmt->bind_param("is", $idturno, $mensaje);
    $stmt->execute();
    $stmt->close();
    return true; 
}
function modificarTurno($enlace, $idturno, $idmedico, $nuevaFecha, $nuevaHora)
{
    $stmt = $enlace->prepare("UPDATE turnos SET fecha = ?, hora = ? WHERE idturno = ? AND idmedico = ?");
    $stmt->bind_param("ssii", $nuevaFecha, $nuevaHora, $idturno, $idmedico);
    $stmt->execute();
    $stmt->close();

    $mensaje = "El médico con ID " . $idmedico . " ha modificado el turno con ID " . $idturno . ". Nueva fecha: " . $nuevaFecha . ", Nueva hora: " . $nuevaHora;
    $stmt = $enlace->prepare("INSERT INTO notificaciones (idturno, tipo, mensaje) VALUES (?, 'Modificacion', ?)");
    $stmt->bind_param("is", $idturno, $mensaje);
    $stmt->execute();
    $stmt->close();
    return true;
}
function obtenerNotificaciones($enlace)
{
    $sql = "SELECT idnotificacion, idturno, tipo, mensaje, fecha_creacion, visto FROM notificaciones ORDER BY visto ASC, fecha_creacion DESC";
    $resultado = $enlace->query($sql);
    return $resultado;
}

function marcarNotificacionComoVista($enlace, $idnotificacion)
{
    $stmt = $enlace->prepare("UPDATE notificaciones SET visto = 1 WHERE idnotificacion = ?");
    $stmt->bind_param("i", $idnotificacion);
    $stmt->execute();
    $stmt->close();
}
function generarBotonRetorno() {
    if (isset($_SESSION['tipousuario'])) { // Primero, verifica si la sesión está iniciada
        if ($_SESSION['tipousuario'] == 'Administrador') {
            echo '<button onclick="window.location.href=\'indexadmin.php\'">Regresar al Inicio</button>';
        } elseif ($_SESSION['tipousuario'] == 'Secretario') {
            echo '<button onclick="window.location.href=\'indexsecretario.php\'">Regresar al Inicio</button>';
        } elseif ($_SESSION['tipousuario'] == 'Medico') {
            echo '<button onclick="window.location.href=\'index.php\'">Regresar al Inicio</button>';
        }
    }
    //Si no hay sesión iniciada, no se muestra nada
}
?>