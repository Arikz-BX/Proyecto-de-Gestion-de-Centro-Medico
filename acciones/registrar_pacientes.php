<?php
include('../conexion/conexionbasededatos.php');
$error = '';
$success = '';

//Una vez se suben los datos se activa el envio a la base de datos.
if (isset($_POST['guardar_paciente'])) {
    $nombrepaciente = mysqli_real_escape_string($enlace, $_POST['nombrepaciente']);
    $dni = mysqli_real_escape_string($enlace, $_POST['dni']);
    $obrasocial = mysqli_real_escape_string($enlace, $_POST['obrasocial']);
    $direccion = mysqli_real_escape_string($enlace, $_POST['direccion']);
    $telefono = mysqli_real_escape_string($enlace, $_POST['telefono']);
    $correoelectronico = mysqli_real_escape_string($enlace, $_POST['correoelectronico']);
    $notas = $_POST['notas'];
    
    if (empty($nombrepaciente)) {
        $error = "El nombre del paciente es obligatorio.";
    } elseif (empty($dni) || strlen($dni) < 8) {
        $error = "El DNI es obligatorio y debe tener al menos 8 caracteres.";
    } elseif (empty($obrasocial)) {
        $error = "La obra social es obligatoria.";
    } elseif (empty($direccion)) {
        $error = "La dirección es obligatoria.";
    } elseif (empty($telefono) || strlen($telefono) < 9) {
        $error = "El teléfono es obligatorio y debe tener al menos 9 caracteres.";
    } elseif (!empty($correoelectronico) && !filter_var($correoelectronico, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    }

    if (empty($error)) {
        $stmt = $enlace->prepare("INSERT INTO pacientes (nombrepaciente, dni, obrasocial, direccion, telefono, correoelectronico, notas) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nombrepaciente, $dni, $obrasocial, $direccion, $telefono, $correoelectronico, $notas);

        if ($stmt->execute()) {
            $success = "Paciente registrado correctamente."; //Registra al paciente sin problemas.
            echo "<script>
                    alert('$success');
                    header(../main/listado_pacientes?success=paciente_registrado.php);
                  </script>";
            exit(); 
        } else {
            $error = "Error al registrar el paciente: " . $stmt->error; //No Registra por un error del statement.
            echo "<script>
                    alert('$error');
                    window.location = '../main/pacientes.php';
                  </script>";
            exit();
        }
        $stmt->close();
    } else {
        echo "<script>
                alert('$error'); 
                window.location = '../main/indexadmin.php';
              </script>";
        exit();
    }
}
$enlace->close();
?>