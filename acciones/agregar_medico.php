<?php
session_start();
include('../conexion/conexionbasededatos.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombrecompleto = mysqli_real_escape_string($enlace, $_POST['nombrecompleto']);
    $dni = mysqli_real_escape_string($enlace, $_POST['dni']);
    $consultorio = mysqli_real_escape_string($enlace, $_POST['consultorio'])
    $direccion = mysqli_real_escape_string($enlace, $_POST['direcciondomicilio']);
    $telefono = mysqli_real_escape_string($enlace, $_POST['telefono']);
    $correo = mysqli_real_escape_string($enlace, $_POST['correo']);
    $especialidad = mysqli_real_escape_string($enlace, $_POST['especialidad']);

    $sql_usuario = "SELECT idusuario FROM usuarios WHERE nombreusuario = '$nombrecompleto'"; //Consigue el ID Usuario mediante comparar el Nombre de Usuario con el Nombre Completo
    $resultado_usuario = $enlace->query($sql_usuario);

    if ($resultado_usuario->num_rows == 1) {
        $fila_usuario = $resultado_usuario->fetch_assoc();
        $idusuario = $fila_usuario['idusuario'];
        $sql = "INSERT INTO medicos (nombrecompleto, dni, consultorio, direcciondomicilio, telefono, correo, especialidad, idusuario) 
                VALUES ('$nombrecompleto', '$dni', '$consultorio','$direccion', '$telefono', '$correo', '$especialidad', '$idusuario')";   

    if ($enlace->query($sql) === TRUE) {
        echo "<script>
            alert('Médico agregado correctamente.');
            window.location.href = '../main/medicos.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al agregar médico: " . $enlace->error . "');
            window.location.href = '../main/medicos.php';
        </script>";
    }
  } else {
    echo "<script>
        alert('Error: No se encontró el usuario con el nombre de usuario proporcionado.');
        window.location.href = '../main/agregar-medico.php';
    </script>"; // Redirige al formulario de agregar médico
   }
} else {
    echo "<script>
        alert('Acceso no permitido.');
        window.location.href = '../main/medicos.php';
    </script>";
}

$enlace->close();
?>