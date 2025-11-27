<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas - Clínica del Bienestar</title>
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
    $medicosDAO = new medicoDAO();

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
            <h1>Gestión de Citas Médicas</h1>
            <p>Administra y gestiona todas las citas programadas en la clínica</p>
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

                                <td><?php echo $cita['Paciente']; ?></td>

                                <td><?php echo $cita['Especialidad_Nombre']; ?></td>

                                <td>
                                    <!--
                                    <button class="btn btn-edit edit-cita"
                                        data-id="<?= $cita['Id_Citas'] ?>"
                                        data-fecha="<?= $cita['Fecha'] ?>"
                                        data-hora="<?= $cita['Hora'] ?>"
                                        data-paciente-id="<?= $cita['Paciente'] ?>"
                                        data-medico-id="<?= $cita['Especialidad_Nombre'] ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    -->

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

    <!-- Modal de Edición de Cita -->
    <div class="modal" id="editarCitaModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Cita Médica</h3>
                <button class="close-modal">&times;</button>
            </div>
            <form id="form-editar-cita" method="POST" action="../backend/controllers/editar_citas.php">

                <div class="modal-body">

                    <input type="hidden" id="edit-id" name="id">

                    <div class="form-group">

                        <label for="edit-paciente">Paciente</label>

                        <div class="select-wrapper">
                            <select id="edit-paciente" name="paciente" class="form-control select" required>
                                <option value="">Seleccionar paciente...</option>

                                <?php
                                $pacientes = $pacienteDAO->consultarIdPaciente('');
                                while ($p = $pacientes->fetch_assoc()):
                                ?>
                                    <option value="<?= $p['Nombre'] . " " . $p['Apellido_Paterno'] . " " . $p['Apellido_Materno'] ?>">
                                        <?= $p['Nombre'] . " " . $p['Apellido_Paterno'] . " " . $p['Apellido_Materno'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="edit-fecha">Fecha de la Cita</label>
                            <input type="date" id="edit-fecha" name="fecha" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-hora">Hora</label>
                            <input type="time" id="edit-hora" name="hora" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-medico">Médico</label>
                            <div class="select-wrapper">

                                <select id="edit-medico" name="medico" class="form-control select" required>
                                    <option value="">Seleccionar médico...</option>
                                    <?php
                                    $medicos = $medicosDAO->consultarMedicos('');
                                    while ($p = $medicos->fetch_assoc()):
                                    ?>
                                        <option value="<?= $p['Nombre'] . " " . $p['Apellido_Paterno'] . " " . $p['Apellido_Materno'] . " - " . $p['Especialidad'] ?>">
                                            <?= $p['Nombre'] . " " . $p['Apellido_Paterno'] . " " . $p['Apellido_Materno'] . " - " . $p['Especialidad'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-outline close-modal">Cancelar</button>
                    <button type="submit" class="btn btn-edit">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal de edición
        const editarModal = document.getElementById('editarCitaModal');
        const editButtons = document.querySelectorAll('.edit-cita');
        //const deleteButtons = document.querySelectorAll('.delete-cita');
        const closeButtons = document.querySelectorAll('.close-modal');
        const editForm = document.getElementById('form-editar-cita');

        // Funcionalidad del botón editar
        editButtons.forEach(button => {
            button.addEventListener('click', function() {

                // Cargar ID de la cita
                document.getElementById('edit-id').value = this.dataset.id;

                // Cargar fecha y hora
                document.getElementById('edit-fecha').value = this.dataset.fecha;
                document.getElementById('edit-hora').value = this.dataset.hora;

                // Seleccionar paciente
                document.getElementById('edit-paciente').value = this.dataset.pacienteId;

                // Seleccionar médico
                document.getElementById('edit-medico').value = this.dataset.medicoId;

                // Mostrar modal
                editarModal.style.display = 'flex';
            });
        });

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

        // Envío del formulario de edición

        // Cerrar modales
        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                editarModal.style.display = 'none';
            });
        });
    </script>

</body>

</html>