<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_POST['logout'])) {
   $_SESSION = array();
   if (ini_get("session.use_cookies")) {
       $params = session_get_cookie_params();
       setcookie(session_name(), '', time() - 42000,
           $params["path"], $params["domain"],
           $params["secure"], $params["httponly"]
       );
   }
   session_destroy();
   // Redirige al usuario a la página de inicio de sesión
   header("Location: inicio-sesion.php");
   exit();
}
function generarBotonRetorno() {
   $pagina_objetivo = '';

    if (isset($_SESSION['tipousuario'])) { // Primero, verifica si la sesión está iniciada
        if ($_SESSION['tipousuario'] == 'Administrador') {
            $pagina_objetivo = 'indexadmin.php';
        } elseif ($_SESSION['tipousuario'] == 'Secretario') {
            $pagina_objetivo = 'indexsecretario.php';
        } elseif($_SESSION['tipousuario'] == 'Medico'){
            $pagina_objetivo = 'index.php';
        }
    }
    return '<li><a class="dropdown-item" href="' . htmlspecialchars($pagina_objetivo) . '">' . "Volver a Pagina Principal" . '</a></li>';

    //Si no hay sesión iniciada, no se muestra nada
}
$mostrar_flecha_previa = false;
$flecha_previa_enlace = '';

$pagina_actual = basename($_SERVER['PHP_SELF']);

$mapeo_de_paginas = [
    'agregar-medico.php' => 'medicos.php',
    'modificar-medico.php' => 'medicos.php',
    'agregar-paciente.php' => 'listado_pacientes.php',
    'modificar-paciente.php' => 'listado_pacientes.php',
    'registro-sesion.php' => 'usuarios.php', 
    'modificar-usuario.php' => 'usuarios.php',
    'modificar-turno.php' => 'turnos.php',
    'agregar-turno.php' => 'turnos.php'
];

if (array_key_exists($pagina_actual, $mapeo_de_paginas)) {
    $mostrar_flecha_previa = true;
    $flecha_previa_enlace = $mapeo_de_paginas[$pagina_actual];
}
?>
<header class="d-flex justify-content-end p-3 bg-light border-bottom">
    <div class="d-flex align-items-center">
        <?php if ($mostrar_flecha_previa): ?>
            <!-- Botón "Volver a la lista" -->
            <a href="<?php echo htmlspecialchars($flecha_previa_enlace); ?>" class="btn btn-secondary btn-sm me-2">
                ← Volver a la lista
            </a>
        <?php endif; ?>
    </div>
    <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Usuario: <?php echo htmlspecialchars($_SESSION['nombreusuario']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <li><h6 class="dropdown-header">Hola, <?php echo htmlspecialchars($_SESSION['nombreusuario']); ?>!</h6></li>
                <li><hr class="dropdown-divider"></li>
    
                <?php if ($_SESSION['tipousuario'] == 'Medico' || $_SESSION['tipousuario'] == 'Administrador') { ?>
                    <li><h6 class="dropdown-header">Navegación Médico:</h6></li>
                    <li><a class="dropdown-item" href="index.php">Pagina Principal (Médicos)</a></li>
                    <!--<li style="display: flex; aling-items: center;">
                        <img src="../estilos/agenda.ico" alt="Icono de agenda" style="width: 30px; height: 30px; margin-right: 5px; margin-left: 5px;">
                        <a class="dropdown-item" href="agenda.php">Registro de Agenda</a>
                    </li>-->
                    <!--<li style="display: flex; align-items: center;">
                        <img src="../estilos/medicosturnos.ico" alt="Icono de turnos" style="width: 30px; height: 30px; margin-right: 5px; margin-left: 5px;">
                        <a class="dropdown-item" href="turnos.php">Gestión de Turnos</a>
                        <img src="../estilos/medicos.ico" alt="Icono de medicos" style="width: 30px; height: 30px; margin-right: 5px; margin-left: 5px;">
                    </li>-->
                    <li style="display: flex; align-items: center;">
                        <img src="../estilos/medicos.ico" alt="Icono de medicos" style="width: 30px; height: 30px; margin-right: 5px; margin-left: 5px;">
                        <a class="dropdown-item" href="medicos.php">Gestión de Médicos</a>
                    </li>
                    <li style="display: flex; align-items: center;">
                        <img src="../estilos/medicolista.ico" alt="Icono de pacientes" style="width: 30px; height: 30px; margin-right: 5px; margin-left: 5px;">
                        <a class="dropdown-item" href="listado_pacientes.php">Gestión de Pacientes</a>
                    </li>
                <?php } ?>
    
                <?php if ($_SESSION['tipousuario'] == 'Secretario' || $_SESSION['tipousuario'] == 'Administrador') { ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">Navegación Secretario:</h6></li>
                    <li><a class="dropdown-item" href="indexsecretario.php">Pagina Principal Secretario</a></li>
                    <li style="display: flex; align-items: center;">
                        <img src="../estilos/usuarios.ico" alt="Icono de usuarios" style="width: 30px; height: 30px; margin-right: 5px; margin-left: 5px;">
                        <a class="dropdown-item" href="usuarios.php">Gestión de Usuarios</a>
                    </li>
                    <li style="display: flex; align-items: center;">
                        <img src="../estilos/medicos.ico" alt="Icono de medicos" style="width: 30px; height: 30px; margin-right: 5px; margin-left: 5px;">
                        <a class="dropdown-item" href="medicos.php">Gestión de Médicos</a>
                    </li>
                    <li style="display: flex; align-items: center;">
                        <img src="../estilos/medicolista.ico" alt="Icono de pacientes" style="width: 30px; height: 30px; margin-right: 5px; margin-left: 5px;">
                        <a class="dropdown-item" href="listado_pacientes.php">Gestión de Pacientes</a>
                    </li>
                    <!--<li style="display: flex; align-items: center;">
                        <img src="../estilos/medicosturnos.ico" alt="Icono de turnos" style="width: 30px; height: 30px; margin-right: 5px; margin-left: 5px;">
                        <a class="dropdown-item" href="turnos.php">Gestión de Turnos</a>
                        <img src="../estilos/secretarios.ico" alt="Icono de medicos" style="width: 30px; height: 30px; margin-right: 5px; margin-left: 5px;">
                    </li>-->
                <?php } ?>
    
                <?php if ($_SESSION['tipousuario'] == 'Administrador') { ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">Navegación Admin:</h6></li>
                    <li><a class="dropdown-item" href="indexadmin.php">Pagina Principal Administrador</a></li>
                    <li><a class="dropdown-item" href="indexsecretario.php">Pagina Principal Secretario (vista)</a></li>
                    <?php } ?>
    
                <li><hr class="dropdown-divider"></li>
    
                <!-- <li><a class="dropdown-item" href="../acciones/cerrar_sesion.php">Cerrar Sesión</a></li> -->
                <?php
                $paginas_principales = ['index.php', 'indexadmin.php', 'indexsecretario.php']; 
                if(!in_array($pagina_actual, $paginas_principales)) {
                    echo generarBotonRetorno();
                } ?>
                <li>
                    <form method="post" class="w-100" style="text-align: right;">
                        <button type="submit" method="post" class="dropdown-item btn btn-danger w-100 text-start" name="logout" id="logout">Cerrar Sesion</button>
                    </form>
                </li>
            </ul>
        </div>
</header>