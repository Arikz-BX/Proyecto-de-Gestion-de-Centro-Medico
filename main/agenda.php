<?php date_default_timezone_set('America/Argentina/Buenos_Aires'); //Se establece el horario para que se pueda comparar con los datos a registrar.?> 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link rel="icon" href="../estilos/agenda.ico">
</head>
<body>
    <div class="container">
    <h1>Registro de Disponibilidad</h1>
    <form action="../acciones/guardar_agenda.php" method="post">
        <div class="form-group">
            <label for="fechalaboral" class="form-label">Fecha de Trabajo</label>
            <input type="date" placeholder="Y-m-d" class="form-control" name="fechalaboral" id="fechalaboral" required='true'>
        </div>
        <div class="form-group">
            <label for="hora_inicio" class="form-label">Hora de Inicio:</label>
            <input type="time" placeholder="HH:MM" class="form-control" name="hora_inicio" id="hora_inicio" required='true'>
        </div>
        <div class="form-group">
            <label for="hora_fin" class="form-label">Hora de Salida:</label>
            <input type="time" placeholder="HH:MM" class="form-control" name="hora_fin" id="hora_fin" required='true'>
        </div>
        <button type="submit" name="guardar_agenda">Guardar Disponibilidad</button>
    </form>
    <a href="../main/index.php" class="button">Volver al Panel</a>
    </div>
</body>
</html>