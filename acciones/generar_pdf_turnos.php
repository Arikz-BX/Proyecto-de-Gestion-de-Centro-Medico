<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
ob_start();

session_start();
if (!isset($_SESSION['nombreusuario']) || $_SESSION['tipousuario'] != 'Medico') {
    header("Location: ../main/inicio-sesion.php");
    exit();
}

require_once ('../tcpdf/tcpdf.php'); 
require_once ('../conexion/conexionbasededatos.php');

$idMedico = $_SESSION['idusuario'];
$sql = "SELECT t.fecha, t.lugar, p.nombrepaciente, p.dni
        FROM turnos AS t  -- Alias 't' para la tabla turnos
        INNER JOIN pacientes AS p ON t.idpaciente = p.idpaciente  -- Alias 'p' para la tabla pacientes y la condición de unión
        WHERE t.idmedico = $idMedico
        ORDER BY t.fecha";

echo "Consulta SQL: " . $sql . "<br>";
  
$resultado = $enlace->query($sql);

if (!$resultado) {
    echo "Error al ejecutar la consulta: " . $enlace->error . "<br>";
    exit(); // Detener la ejecución para no generar un PDF vacío o incorrecto
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($idMedico);
$pdf->SetTitle('Listado de Turnos');
$pdf->SetSubject('Turnos Médicos');
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetFont('dejavusans', '', 10);
$pdf->AddPage();

$html = '<h1>Listado de Turnos</h1>';
if ($resultado->num_rows > 0) {
    $html .= '<table border="1">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>DNI Paciente</th>
                        <th>Lugar</th>
                    </tr>
                </thead>
                <tbody>';
    while ($fila = $resultado->fetch_assoc()) {
        $fechaHora = new DateTime($fila['fecha']);
        $fecha = $fechaHora->format('d/m/Y');
        $hora = $fechaHora->format('H:i');
        $html .= '<tr>
                    <td>' . $fecha . '</td>
                    <td>' . $hora . '</td>
                    <td>' . $fila['nombrepaciente'] . '</td>
                    <td>' . $fila['dni'] . '</td>
                    <td>' . $fila['lugar'] . '</td>
                </tr>';
    }
    $html .= '</tbody></table>';
} else {
    $html .= '<p>No tiene turnos asignados.</p>';
}

$pdf->writeHTML($html, true, false, true, false, '');

ob_end_clean();  // Elimina el contenido del búfer
$pdf->Output('turnos_medico_' . $_SESSION['nombreusuario'] . '_' . date('YmdHis') . '.pdf', 'D');
exit;