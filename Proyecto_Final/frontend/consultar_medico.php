<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médicos - Clínica del Bienestar</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/consulta_medico.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- LiveValidation -->
    <script src="https://cdn.jsdelivr.net/gh/Formu8JS/LiveValidateJS@main/livevalidate.js"></script>
    <style>
        .error-item,
        .error-message .error-item,
        div.error-item,
        .error-item[data-error],
        .error-item[style] {
            color: red !important;
            font-weight: 700 !important;
            font-size: 15px !important;
        }
    </style>
</head>

<body>

    <?php
    require_once('navbar_admin.php');

    $medicoDAO = new medicoDAO();

    unset($_SESSION['medico_delete_ok']);
    unset($_SESSION['medico_delete_error']);

    if (!$medicoDAO->hayMedicos()) {
        echo "
        <script>
        Swal.fire({
            icon: 'error',
            title: 'No hay médicos registrados',
            text: 'Debes esperar que al menos un médico sea registrado.'
        }).then(() => {
            window.location.href = 'landing_admin.php';
        });
        </script>";
        exit;
    }

    $datos = $medicoDAO->consultarMedicos('');
    ?>

    <div class="container">
        <div class="header">
            <h1>Nuestros Médicos</h1>
            <p>Conoce al equipo médico especializado de la Clínica del Bienestar</p>
        </div>

        <div class="medicos-container">
            <div class="table-responsive">
                <table class="medicos-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido Paterno</th>
                            <th>Apellido Materno</th>
                            <th>Especialidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos as $cita): ?>
                            <tr>
                                <td><?= $cita['Nombre'] ?></td>
                                <td><?= $cita['Apellido_Paterno'] ?></td>
                                <td><?= $cita['Apellido_Materno'] ?></td>
                                <td><?= $cita['Especialidad'] ?></td>
                                <td>
                                    <button class="btn btn-edit edit-medico"
                                        data-id="<?= $cita['Id_Medicos'] ?>"
                                        data-nombre="<?= $cita['Nombre'] ?>"
                                        data-apellidop="<?= $cita['Apellido_Paterno'] ?>"
                                        data-apellidom="<?= $cita['Apellido_Materno'] ?>"
                                        data-especialidad="<?= $cita['Especialidad'] ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-danger delete-medico"
                                        data-id="<?= $cita['Id_Medicos'] ?>"
                                        data-nombre="<?= $cita['Nombre'] . ' ' . $cita['Apellido_Paterno'] . ' ' . $cita['Apellido_Materno'] ?>">
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

    <!-- Modal -->
    <div class="modal" id="editarMedicoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Médico</h3>
                <button class="close-modal">&times;</button>
            </div>

            <!-- FORMULARIO REAL -->
            <form id="form-editar-medico" action="../backend/controllers/editar_medico.php" method="POST">
                <div class="modal-body">

                    <input type="hidden" id="edit-id" name="id">

                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" id="edit-nombre" name="nombre">
                    </div>

                    <div class="form-group">
                        <label>Apellido Paterno</label>
                        <input type="text" id="edit-apellido-paterno" name="apellido_paterno">
                    </div>

                    <div class="form-group">
                        <label>Apellido Materno</label>
                        <input type="text" id="edit-apellido-materno" name="apellido_materno">
                    </div>

                    <div class="form-group">
                        <label>Especialidad</label>
                        <select id="edit-especialidad" name="especialidad">
                            <option value="">Seleccione una especialidad</option>
                            <option value="Pediatra">Pediatra</option>
                            <option value="Cirujano">Cirujano</option>
                            <option value="Internista">Internista</option>
                            <option value="General">General</option>
                        </select>
                    </div>

                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-outline close-modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>

            </form>
        </div>
        <!-- Validaciones con LiveValidation -->
        <script>
            const nombreInput = document.getElementById('edit-nombre');
            const apellidoPInput = document.getElementById('edit-apellido-paterno');
            const apellidoMInput = document.getElementById('edit-apellido-materno');
            const especialidadInput = document.getElementById('edit-especialidad');

            const soloLetras = /^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/;

            const nombreValidator = addLiveValidation(nombreInput, [{
                    required: true,
                    requiredMessage: "El nombre es obligatorio"
                },
                {
                    pattern: soloLetras,
                    patternMessage: "El nombre solo puede contener letras"
                },
                {
                    minLength: 2,
                    minLengthMessage: "El nombre debe tener al menos 2 letras"
                },
                {
                    maxLength: 40,
                    maxLengthMessage: "El nombre no puede exceder 40 letras"
                }
            ], {
                displayMode: "classic",
            });

            const apellidoPValidator = addLiveValidation(apellidoPInput, [{
                    required: true,
                    requiredMessage: "El apellido paterno es obligatorio"
                },
                {
                    pattern: soloLetras,
                    patternMessage: "El apellido paterno solo puede contener letras"
                },
                {
                    minLength: 2,
                    minLengthMessage: "Debe tener al menos 2 letras"
                },
                {
                    maxLength: 40,
                    maxLengthMessage: "No puede exceder 40 letras"
                }
            ], {
                displayMode: "classic",
            });

            const apellidoMValidator = addLiveValidation(apellidoMInput, [{
                    required: true,
                    requiredMessage: "El apellido materno es obligatorio"
                },
                {
                    pattern: soloLetras,
                    patternMessage: "El apellido materno solo puede contener letras"
                },
                {
                    minLength: 2,
                    minLengthMessage: "Debe tener al menos 2 letras"
                },
                {
                    maxLength: 40,
                    maxLengthMessage: "No puede exceder 40 letras"
                }
            ], {
                displayMode: "classic",
            });

            const especialidadValidator = addLiveValidation(especialidadInput, [{
                    required: true,
                    requiredMessage: "La especialidad es obligatoria"
                },
                {
                    custom: (value) => value !== "",
                    customMessage: "Debe seleccionar una especialidad válida"
                }
            ], {
                displayMode: "classic",
            });
        </script>

        <script>
            // Elimina LOS MENSAJES DE ÉXITO que LiveValidate crea
            const observer = new MutationObserver(() => {
                document.querySelectorAll(".success-message").forEach(msg => msg.remove());
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        </script>

        <!--SweetAlert-->
        <?php

        if (isset($_SESSION['medico_edit']) && $_SESSION['medico_edit'] == true) {
            echo "<script>
        Swal.fire({
            title: 'Medico Modificado!',
            text: 'La informacion ha sido guardada correctamente.',
            icon: 'success',
            confirmButtonColor: '#8B0035'
        });
    </script>";
            unset($_SESSION['medico_edit']);
        }

        if (isset($_SESSION['medico_edit_error']) && $_SESSION['medico_edit_error'] == true) {

            $lista = "";
            if (isset($_SESSION['errores_lista'])) {
                foreach ($_SESSION['errores_lista'] as $err) {
                    $lista .= "<li>$err</li>";
                }
            }

            echo "<script> 
        Swal.fire({
            title: 'Errores encontrados',
            html: '<ul style=\"text-align: left; color:#d33;\">$lista</ul>',
            icon: 'error', 
            confirmButtonColor: '#8B0035' 
        }); 
    </script>";

            unset($_SESSION['medico_edit_error']);
            unset($_SESSION['errores_lista']);
        }
        ?>
    </div>

    <script>
        const editarModal = document.getElementById('editarMedicoModal');
        const editButtons = document.querySelectorAll('.edit-medico');
        const closeButtons = document.querySelectorAll('.close-modal');

        // ABRIR MODAL Y CARGAR DATOS
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {

                document.getElementById('edit-id').value = this.dataset.id;
                document.getElementById('edit-nombre').value = this.dataset.nombre;
                document.getElementById('edit-apellido-paterno').value = this.dataset.apellidop;
                document.getElementById('edit-apellido-materno').value = this.dataset.apellidom;
                document.getElementById('edit-especialidad').value = this.dataset.especialidad;

                editarModal.style.display = 'flex';
            });
        });

        // Cerrar modal
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                editarModal.style.display = 'none';
            });
        });

        window.addEventListener('click', e => {
            if (e.target === editarModal) editarModal.style.display = 'none';
        });
    </script>

    <script>
        document.querySelectorAll('.delete-medico').forEach(btn => {

            btn.addEventListener('click', function() {

                let id = this.dataset.id;
                let nombre = this.dataset.nombre;

                Swal.fire({
                    title: "¿Eliminar médico?",
                    html: "¿Seguro que deseas eliminar a:<br><b>" + nombre + "</b>?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {

                        // Enviar a PHP para borrar
                        window.location.href = "../backend/controllers/eliminar_medico.php?id=" + id;

                    }
                });

            });

        });
    </script>

    <?php
    if (isset($_SESSION['medico_delete_ok'])) {
        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Médico eliminado',
            text: 'El registro ha sido borrado.',
            confirmButtonColor: '#8B0035'
        });
    </script>";
        unset($_SESSION['medico_delete_ok']);
    }

    if (isset($_SESSION['medico_delete_error'])) {
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{$_SESSION['medico_delete_error']}',
            confirmButtonColor: '#8B0035'
        });
    </script>";
        unset($_SESSION['medico_delete_error']);
    }
    ?>

</body>

</html>