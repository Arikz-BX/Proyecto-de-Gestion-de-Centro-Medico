<?php
session_start();
include('../conexion/conexionbasededatos.php');
$idusuario_precargado = null;
function listMedicos($enlace)
{
// Usamos LEFT JOIN para incluir usuarios que son médicos pero aún no tienen un registro completo en 'medicos'. 
    $medicossql = "SELECT u.idusuario, u.nombrecompleto, 
               m.idmedico, m.matricula, m.dni, m.consultorio, m.direcciondomicilio, m.telefono, m.correo, m.especialidad, m.estado
        FROM usuarios u
        LEFT JOIN medicos m ON u.idusuario = m.idusuario
        WHERE u.tipousuario = 'Medico'
        AND (
                m.idmedico IS NULL OR               -- Si no hay registro en 'medicos' (perfil incompleto)
                m.matricula = '' OR m.matricula = '0' OR -- Matrícula vacía o '0' (indicador de incompleto)
                m.dni IS NULL OR m.dni = '' OR                        -- DNI vacío
                m.consultorio IS NULL OR m.consultorio = '' OR              -- Consultorio vacío
                m.direcciondomicilio IS NULL OR m.direcciondomicilio = '' OR  -- Dirección vacía
                m.telefono IS NULL OR m.telefono = '' OR                      -- Teléfono vacío
                m.correo IS NULL OR m.correo = '' OR               -- Correo vacío
                m.especialidad IS NULL OR m.especialidad = ''         -- Especialidad vacía
            )
        ORDER BY u.nombrecompleto ASC"; 
    $resultado = $enlace->query($medicossql);
    return $resultado;
}
$selected_nombrecompleto = '';
$selected_matricula = '';
$selected_dni = '';
$selected_consultorio = '';
$selected_direccion = '';
$selected_telefono = '';
$selected_correo = '';
$selected_especialidad = '';
// Obtener la lista de médicos para el dropdown
$medicos_para_formulario = listMedicos($enlace);
if (isset($_GET['idusuario_precargado']) && is_numeric($_GET['idusuario_precargado'])) {
    $idusuario_precargado = (int)$_GET['idusuario_precargado'];

    // Buscar los datos de ese médico específico
    $stmt_precarga = $enlace->prepare("SELECT u.idusuario, u.nombrecompleto, 
                                              m.matricula, m.dni, m.consultorio, m.direcciondomicilio, m.telefono, m.correo, m.especialidad 
                                      FROM usuarios u
                                      LEFT JOIN medicos m ON u.idusuario = m.idusuario
                                      WHERE u.idusuario = ? AND u.tipousuario = 'Medico'");
    if ($stmt_precarga) {
        $stmt_precarga->bind_param("i", $idusuario_precargado);
        $stmt_precarga->execute();
        $resultado_precarga = $stmt_precarga->get_result();
        if ($resultado_precarga->num_rows > 0) {
            $datos_medico = $resultado_precarga->fetch_assoc();
            
            $selected_idusuario = htmlspecialchars($datos_medico['idusuario'] ?? '', ENT_QUOTES, 'UTF-8');
            $selected_nombrecompleto = htmlspecialchars($datos_medico['nombrecompleto'] ?? '', ENT_QUOTES, 'UTF-8');
            $selected_matricula = htmlspecialchars($datos_medico['matricula'] ?? '', ENT_QUOTES, 'UTF-8');
            $selected_dni = htmlspecialchars($datos_medico['dni'] ?? '', ENT_QUOTES, 'UTF-8');
            $selected_consultorio = htmlspecialchars($datos_medico['consultorio'] ?? '', ENT_QUOTES, 'UTF-8');
            $selected_direccion = htmlspecialchars($datos_medico['direcciondomicilio'] ?? '', ENT_QUOTES, 'UTF-8');
            $selected_telefono = htmlspecialchars($datos_medico['telefono'] ?? '', ENT_QUOTES, 'UTF-8');
            $selected_correo = htmlspecialchars($datos_medico['correo'] ?? '', ENT_QUOTES, 'UTF-8');
            $selected_especialidad = htmlspecialchars($datos_medico['especialidad'] ?? '', ENT_QUOTES, 'UTF-8');
        }
        $stmt_precarga->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado: Agregar Médico</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link rel="icon" href="../estilos/medicoslista.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
    <?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
    <div class="container">
        <h1 class="medico">Agregar Médico</h1>
        <div class="formulario">
            <form action="../acciones/agregar_medico.php" method="post">
                <input type="hidden" name="idusuario_seleccionado" id="idusuario_seleccionado_hidden" value="<?php echo $idusuario_precargado; ?>">
                <label for="selectMedico">Seleccione Médico a Completar:</label>
                <select name="selectMedico" id="selectMedico" class="form-select mb-3" required>
                    <option value="">-- Seleccione un usuario médico --</option>
                    <?php 
                    if ($medicos_para_formulario && $medicos_para_formulario->num_rows > 0) {
                        while ($medico = $medicos_para_formulario->fetch_assoc()) {
                            // Marca como "selected" si es el médico precargado desde la URL
                            $selected = ($selected_idusuario == $medico['idusuario']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($medico['idusuario']) . "' " . $selected . 
                                 " data-nombrecompleto='" . htmlspecialchars($medico['nombrecompleto'] ?? '') . "'" .
                                 " data-matricula='" . htmlspecialchars($medico['matricula'] ?? '') . "'" .
                                 " data-dni='" . htmlspecialchars($medico['dni'] ?? '') . "'" .
                                 " data-consultorio='" . htmlspecialchars($medico['consultorio'] ?? '') . "'" .
                                 " data-direccion='" . htmlspecialchars($medico['direcciondomicilio'] ?? '') . "'" .
                                 " data-telefono='" . htmlspecialchars($medico['telefono'] ?? '') . "'" .
                                 " data-correo='" . htmlspecialchars($medico['correo'] ?? '') . "'" .
                                 " data-especialidad='" . htmlspecialchars($medico['especialidad'] ?? '') . "'" .
                                 ">" . htmlspecialchars($medico['nombreusuario']) . " (Matrícula: " . htmlspecialchars($medico['matricula'] ?? 'Pendiente') . ")</option>";
                        }
                    } else {
                        echo "<option value=''>No hay usuarios médicos para completar o modificar.</option>";
                    }
                    ?>
                </select>
                <label for="nombrecompleto">Nombre Medico:</label>
                <input type="text" id="nombrecompleto" name="nombrecompleto" class="form-control" value="<?php echo $selected_nombrecompleto; ?>" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$" required>
                <label for="dni">DNI:</label>
                <input type="text" id="dni" minlength="8" name="dni" class="form-control" value="<?php echo $selected_dni; ?>" pattern="^\d{8}$" required>
                <label for="consultorio">Consultorio:</label>
                <input type="text" id="consultorio" minlength="12" name="consultorio" class="form-control" value="<?php echo $selected_consultorio; ?>" pattern="^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s.,#-]*$" required>
                <label for="direccion">Dirección de Domicilio:</label>
                <input type="text" id="direccion" minlength="12" name="direccion" class="form-control" value="<?php echo $selected_direccion; ?>" pattern="^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s.,#-]*$" required>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" minlength="9" maxlength="10" value="<?php echo $selected_telefono; ?>" name="telefono" class="form-control" pattern="^\+?\d{9,15}$" required>
                <label for="correo">Correo:</label>
                <input type="email" id="correo" minlength="16" name="correo" class="form-control" value="<?php echo $selected_correo; ?>" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" required>
                <label for="especialidad">Especialidad:</label>
                <input type="text" id="especialidad" name="especialidad" class="form-control" value="<?php echo $selected_especialidad; ?>" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s,-]+$" required>
                <button type="submit" class="button">Agregar Médico</button>
                <!--<a href="medicos.php" class="button" onclick="return confirm('¿Estás seguro de que deseas cancelar el Registro del Medico?');">Cancelar</a>-->
            </form>
        </div>
    </div>
<div class= footer>
    <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script>
        // Script para precargar el formulario al seleccionar un médico del dropdown
        document.getElementById('selectMedico').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            // Actualiza el campo oculto con el idusuario seleccionado
            document.getElementById('idusuario_seleccionado_hidden').value = selectedOption.value;
            
            // Precarga los demás campos del formulario usando los data-atributos
            document.getElementById('nombrecompleto').value = selectedOption.dataset.nombrecompleto || '';
            document.getElementById('matricula').value = selectedOption.dataset.matricula || '';
            document.getElementById('dni').value = selectedOption.dataset.dni || '';
            document.getElementById('consultorio').value = selectedOption.dataset.consultorio || '';
            document.getElementById('direccion').value = selectedOption.dataset.direccion || '';
            document.getElementById('telefono').value = selectedOption.dataset.telefono || '';
            document.getElementById('correo').value = selectedOption.dataset.correo || '';
            document.getElementById('especialidad').value = selectedOption.dataset.especialidad || '';
        });

        // Asegurarse de que el formulario se precargue si se viene con un idusuario_precargado por URL
        window.onload = function() {
            const selectMedico = document.getElementById('selectMedico');
            const idusuarioHidden = document.getElementById('idusuario_seleccionado_hidden');

            if (idusuarioHidden.value) { // Si el hidden input ya tiene un valor (viene de PHP por GET)
                // Esto simula la selección del dropdown para precargar el resto de los campos.
                selectMedico.value = idusuarioHidden.value;
                selectMedico.dispatchEvent(new Event('change')); // Dispara el evento 'change'
            }
        };
    </script>
    <script>
    const form = document.getElementById('formularioMedico');
    let formInicial = new FormData(form);

    window.addEventListener('DOMContentLoaded', () => {
        formInicial = new FormData(form);
    });

    function detectaCambios() {
        const formActual = new FormData(form);
        for (let [key, value] of formInicial.entries()) {
            if (formActual.get(key) !== value) {
                return true;
            }
        }
        return false;
    }

    const enlacesRetorno = document.querySelectorAll('a.boton-retorno');
    enlacesRetorno.forEach(enlace => {
        enlace.addEventListener('click', function (e) {
            if (detectaCambios()) {
                const confirmacion = confirm("¿Estás seguro de que deseas cancelar los cambios?");
                if (!confirmacion) {
                    e.preventDefault();
                }
            }
        });
    });
    </script>
</body>
</html>