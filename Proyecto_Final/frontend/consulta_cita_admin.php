<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas - Clínica del Bienestar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/consulta_cita_admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php
    require_once('navbar_admin.php');

    $citaDAO = new citaDAO();
    $pacienteDAO = new pacienteDAO();

    // Traer todas las citas del sistema
    $datos = $citaDAO->consultaCitasVista('');


    // Si no hay citas, mostrar mensaje y redirigir
    if (mysqli_num_rows($datos) === 0) {
        echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
    Swal.fire({
        icon: 'warning',
        title: 'Sin citas registradas',
        text: 'Aún no han hecho citas programadas.',
        confirmButtonText: 'Entendido'
    }).then(() => {
        window.location.href = 'landing_admin.php';
    });
    </script>";
        exit;
    }

    ?>

    <div class="container">
        <div class="header">
            <h1>Eliminar Citas Programadas</h1>
            <p>Elimine las citas de pacientes</p>
        </div>

        <div class="citas-container">
            <div class="table-responsive">
                <table class="citas-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Médico</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos as $cita): ?>
                            <tr>
                                <td><?php echo date("d/m/Y", strtotime($cita['Fecha'])); ?></td>
                                <td><?php echo date("h:i A", strtotime($cita['Hora'])); ?></td>

                                <!-- Nombre del PACIENTE -->
                                <td><?php echo $cita['Paciente']; ?></td>

                                <!-- Nombre del MÉDICO -->
                                <td><?php echo $cita['Medico']; ?></td>

                                <td>


                                    <!-- ELIMINAR -->
                                    <button class="btn btn-danger delete-cita"
                                        data-id="<?= $cita['Id_Citas'] ?>"
                                        data-nombre="Cita del paciente <?= $cita['Paciente'] ?>">
                                        <i class="fas fa-trash"></i> Eliminar
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
    <!-- Modal de Editar Cita -->
    <div class="modal" id="citaModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Cita</h3>
                <button class="close-modal">&times;</button>
            </div>

            <form action="editar_cita.php" method="POST">

                <div class="modal-body">

                    <!-- ID CITA OCULTO -->
                    <input type="hidden" name="id_cita" id="modal-id">

                    <div class="info-group">
                        <label>Paciente:</label>
                        <input type="text" id="modal-paciente" name="paciente" readonly>
                    </div>

                    <div class="info-group">
                        <label>Fecha de la Cita:</label>
                        <input type="date" id="modal-fecha" name="fecha" required>
                    </div>

                    <div class="info-group">
                        <label>Hora:</label>
                        <input type="time" id="modal-hora" name="hora" required>
                    </div>

                    <div class="info-group">
                        <label>Médico:</label>
                        <select id="modal-medico" name="medico" required>
                            <option value="" disabled selected>Seleccionar médico...</option>
                            
                            <?php
                            /*
                            // Traer lista de médicos
                            require_once('../backend/dao/medicoDAO.php');
                            $medicoDAO = new medicoDAO();
                            $listaMedicos = $medicoDAO->consultarMedicos();

                            foreach ($listaMedicos as $med) {
                                echo "<option value='{$med['Id_Medico']}'>{$med['Nombre']} - {$med['Especialidad']}</option>";
                            }*/
                            ?>
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn btn-edit">Guardar Cambios</button>
                    <button type="button" class="btn btn-outline close-modal">Cancelar</button>
                </div>
            </form>

        </div>
    </div>


    <script>
        const modal = document.getElementById('citaModal');
        const viewButtons = document.querySelectorAll('.edit-cita');
        const closeButtons = document.querySelectorAll('.close-modal');

        viewButtons.forEach(button => {
            button.addEventListener('click', function() {

                document.getElementById('modal-id').value = this.dataset.id;
                document.getElementById('modal-paciente').value = this.dataset.paciente;
                document.getElementById('modal-fecha').value = this.dataset.fecha;
                document.getElementById('modal-hora').value = this.dataset.hora;

                // Seleccionar el médico correcto en el select
                const medicoSelect = document.getElementById('modal-medico');
                for (let option of medicoSelect.options) {
                    if (option.text.includes(this.dataset.medico)) {
                        option.selected = true;
                        break;
                    }
                }

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

    <script>
        // Botones eliminar
        const deleteButtons = document.querySelectorAll('.delete-cita');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {

                const id = this.dataset.id;
                const nombre = this.dataset.nombre;

                Swal.fire({
                    title: "¿Eliminar cita?",
                    text: "¿Deseas eliminar " + nombre + "?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.isConfirmed) {

                        // Enviar a PHP para eliminar
                        window.location.href = "../backend/controllers/eliminar_cita.php?id=" + id;

                    }
                });

            });
        });
    </script>


    <script>
        <?php

        if (isset($_GET['delete']) && $_GET['delete'] === "ok") {
            echo "
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Cita eliminada',
            text: 'La cita fue eliminada correctamente.'
        });
    </script>";
        }

        if (isset($_GET['delete']) && $_GET['delete'] === "error") {
            echo "
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo eliminar la cita.'
        });
    </script>";
        }


        ?>
    </script>

</body>

</html>