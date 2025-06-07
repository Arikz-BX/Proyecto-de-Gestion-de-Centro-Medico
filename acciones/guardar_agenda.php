<?php
session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
include('../conexion/conexionbasededatos.php');

if (isset($_POST['guardar_agenda'])) {
    if (!isset($_SESSION['idmedico'])) {
        echo "<script>
                alert('Error: Sesión de médico no encontrada.');
                window.location = '../inicio-sesion.php';
              </script>";
        exit();
    }
    if (!isset($_POST['fechalaboral'], $_POST['hora_inicio'], $_POST['hora_fin'])) {
        echo "<script>
                alert('Error: Faltan datos para guardar la agenda.');
              </script>";
    }

    $idmedico = $_SESSION['idmedico'];
    $fechalaboral = $_POST['fechalaboral'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $fecha_actual = date("Y-m-d"); 
    if ($fechalaboral < $fecha_actual) {
        echo "<script>
            alert('La fecha de la agenda no puede ser anterior a la fecha actual.');
            </script>";
        exit();
    } elseif ($fechalaboral == $fecha_actual) { //Verifica que si es el mismo dia que la hora no este atrasada.
        $hora_actual = date("H:i"); // Obtener hora actual en formato HH:MM
        if ($hora_inicio < $hora_actual) {
            echo "<script>
                    alert('Error: Si la agenda es para hoy, la hora de inicio no puede ser anterior a la hora actual.');
                  </script>";
            exit();
        }
    }
     if ($hora_inicio >= $hora_fin) {
        echo "<script>
        alert('La Hora de Inicio no puede ser igual o posterior a la Hora de Salida.');
        </script>";
    exit();
    }

    
    
    if (empty($fechalaboral) || empty($hora_inicio) || empty($hora_fin)) {
        $error = "Todos los campos son obligatorios";
    } elseif ($hora_inicio >= $hora_fin){
        $error = "La hora de Inicio debe ser anterior a la hora de Salida.";
    } else {
        $error = "";
    }

    if (empty($error)) {
        $consulta_medico = "SELECT idmedico FROM medicos WHERE idmedico = ?";
        $stmt_medico = $enlace->prepare($consulta_medico);
        $stmt_medico->bind_param("i", $idmedico);
        $stmt_medico->execute();
        $resultado_medico = $stmt_medico->get_result();

        if($resultado_medico->num_rows > 0) {
            $stmt = $enlace->prepare("INSERT INTO agenda(idmedico, fechalaboral, hora_inicio, hora_final) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $idmedico, $fechalaboral, $hora_inicio, $hora_fin);

            if($stmt->execute()) {
                echo "<script>
                        alert('Disponibilidad registrada correctamente.');
                        window.location = '../main/index.php';
                    </script>";
                exit();
            } else {
                $error = "Error al registrar la disponibilidad: " . $stmt->error;
                echo "<script>
                        alert('$error');
                    </script>";
                exit();
            }
            $stmt->close();
        } else {
            $error = "Error: El ID de médico proporcionado no existe.";
            echo "<script>
                    alert('$error');
                </script>";
            exit();
        }
        $stmt_medico->close();
    } else {
        echo "<script>
                alert('$error');
            </script>";
            exit();
    }
}
$enlace->close();
?>