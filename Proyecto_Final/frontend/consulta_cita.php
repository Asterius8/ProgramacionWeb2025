<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas - Clínica del Bienestar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/consulta_cita.css">

</head>

<body>

<?php
require_once('navbar_paciente.php');

$id=$_SESSION['Id_p'];
$citaDAO = new citaDAO();

// Traer SOLO las citas del paciente que está logeado
$datos = $citaDAO->consultarCitasPorPaciente($id);

// Si no hay citas, mostrar mensaje y redirigir
if (empty($datos)) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
    Swal.fire({
        icon: 'warning',
        title: 'Sin citas registradas',
        text: 'Aún no tienes citas programadas.',
        confirmButtonText: 'Entendido'
    }).then(() => {
        window.location.href = 'landing_paciente.php';
    });
    </script>";
    exit;
}

// Nombre del paciente desde la sesión
$nombrePaciente = $_SESSION['ncp'];
?>

<div class="container">
    <div class="header">
        <h1>Mis Citas Programadas</h1>
        <p>Consulta todas tus citas médicas</p>
    </div>

    <div class="citas-container">
        <div class="table-responsive">
            <table class="citas-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Médico</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos as $cita): ?>
                        <tr>
                            <td><?php echo date("d/m/Y", strtotime($cita['Fecha'])); ?></td>
                            <td><?php echo date("h:i A", strtotime($cita['Hora'])); ?></td>
                            <td><?php echo $cita['Especialidad_Nombre']; ?></td>
                            <td>
                                <button class="btn btn-primary view-details"
                                    data-fecha="<?php echo date("d/m/Y", strtotime($cita['Fecha'])); ?>"
                                    data-hora="<?php echo date("h:i A", strtotime($cita['Hora'])); ?>"
                                    data-medico="<?php echo $cita['Especialidad_Nombre']; ?>">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Detalles de Cita -->
<div class="modal" id="citaModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalles de la Cita</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="info-group">
                <label>Paciente:</label>
                <p id="modal-paciente"><?php echo $nombrePaciente; ?></p>
            </div>
            <div class="info-group">
                <label>Fecha de la Cita:</label>
                <p id="modal-fecha"></p>
            </div>
            <div class="info-group">
                <label>Hora:</label>
                <p id="modal-hora"></p>
            </div>
            <div class="info-group">
                <label>Médico:</label>
                <p id="modal-medico"></p>
            </div>
            <div class="info-group">
                <label>Especialidad:</label>
                <p id="modal-especialidad"></p>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-outline close-modal">Cerrar</button>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById('citaModal');
const viewButtons = document.querySelectorAll('.view-details');
const closeButtons = document.querySelectorAll('.close-modal');

// Nombre del paciente ya está en PHP
const nombrePaciente = "<?php echo $nombrePaciente; ?>";

viewButtons.forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('modal-paciente').textContent = nombrePaciente;
        document.getElementById('modal-fecha').textContent = this.dataset.fecha;
        document.getElementById('modal-hora').textContent = this.dataset.hora;

        // Separar nombre del médico y especialidad
        const medicoYEspecialidad = this.dataset.medico.split(" - ");
        document.getElementById('modal-medico').textContent = medicoYEspecialidad[0] || "";
        document.getElementById('modal-especialidad').textContent = medicoYEspecialidad[1] || "";

        modal.style.display = 'flex';
    });
});

closeButtons.forEach(button => {
    button.addEventListener('click', () => {
        modal.style.display = 'none';
    });
});

window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});
</script>

</body>
</html>
