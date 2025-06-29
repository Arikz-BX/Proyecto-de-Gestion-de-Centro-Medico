<?php date_default_timezone_set('America/Argentina/Buenos_Aires'); //Se establece el horario para que se pueda comparar con los datos a registrar.?> 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link rel="icon" href="../estilos/agenda.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
<?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
<div class="container">
    <h1>Registro de Disponibilidad</h1>
        <div class="formulario">
            <form action="../acciones/guardar_agenda.php" method="post">
                <label for="fechalaboral" class="form-label">Fecha de Trabajo</label>
                <input type="date" placeholder="Y-m-d" class="form-control" name="fechalaboral" id="fechalaboral" required='true'>
                <label for="hora_inicio" class="form-label">Hora de Inicio:</label>
                <input type="time" placeholder="HH:MM" class="form-control" name="hora_inicio" id="hora_inicio" required='true'>
                <label for="hora_fin" class="form-label">Hora de Salida:</label>
                <input type="time" placeholder="HH:MM" class="form-control" name="hora_fin" id="hora_fin" required='true'>
                <button type="submit" name="guardar_agenda">Guardar Disponibilidad</button>
            </form>
        </div>    
    <a href="../main/index.php" class="button">Volver al Panel</a>
</div>
<div class= footer>
        <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>