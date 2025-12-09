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
    <style>
        .search-container {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .search-box {
            position: relative;
            max-width: 500px;
            margin: 0 auto;
        }

        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: 'Montserrat', sans-serif;
        }

        .search-box input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .search-box i {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }

        .results-count {
            text-align: center;
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }
    </style>
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

        <!-- Caja de búsqueda -->
        <div class="search-container">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Buscar por fecha, hora, paciente o médico...">
                <i class="fas fa-search"></i>
            </div>
            <div class="results-count" id="resultsCount"></div>
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
                    <tbody id="citasTableBody">

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
                <div class="no-results" id="noResults" style="display: none;">
                    <i class="fas fa-search" style="font-size: 48px; color: #ddd; margin-bottom: 10px;"></i>
                    <p>No se encontraron resultados que coincidan con tu búsqueda</p>
                </div>
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
        // ========================================
        // SISTEMA DE BÚSQUEDA Y FILTRADO DE TABLA
        // ========================================
        
        // Elementos del DOM
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('citasTableBody');
        const noResults = document.getElementById('noResults');
        const resultsCount = document.getElementById('resultsCount');
        const citasTable = document.querySelector('.citas-table');
        
        // Obtener todas las filas de la tabla
        const allRows = Array.from(tableBody.getElementsByTagName('tr'));
        const totalRows = allRows.length;

        // Función principal de búsqueda
        function filtrarTabla() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            // Si no hay término de búsqueda, mostrar todas las filas
            if (searchTerm === '') {
                allRows.forEach(row => {
                    row.style.display = '';
                });
                actualizarContador(totalRows, totalRows);
                mostrarTabla();
                return;
            }

            // Buscar en cada fila
            allRows.forEach(row => {
                // Obtener el texto de cada celda (excepto la última que son botones)
                const cells = row.getElementsByTagName('td');
                let match = false;

                // Buscar en las primeras 4 columnas (Fecha, Hora, Paciente, Médico)
                for (let i = 0; i < cells.length - 1; i++) {
                    const cellText = cells[i].textContent.toLowerCase();
                    
                    // Verificar si el término de búsqueda está en el texto
                    if (cellText.includes(searchTerm)) {
                        match = true;
                        break;
                    }
                }

                // Mostrar u ocultar la fila según el resultado
                if (match) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Actualizar la interfaz según los resultados
            actualizarContador(visibleCount, totalRows);
            
            if (visibleCount === 0) {
                ocultarTabla();
            } else {
                mostrarTabla();
            }
        }

        // Función para actualizar el contador de resultados
        function actualizarContador(visible, total) {
            const citaText = total !== 1 ? 'citas' : 'cita';
            
            if (searchInput.value.trim() === '') {
                resultsCount.textContent = `Mostrando ${total} ${citaText}`;
            } else {
                resultsCount.textContent = `Mostrando ${visible} de ${total} ${citaText}`;
            }
        }

        // Función para mostrar la tabla
        function mostrarTabla() {
            noResults.style.display = 'none';
            citasTable.style.display = '';
        }

        // Función para ocultar la tabla y mostrar mensaje
        function ocultarTabla() {
            noResults.style.display = 'block';
            citasTable.style.display = 'none';
        }

        // Event listener para detectar cambios en el input
        searchInput.addEventListener('input', filtrarTabla);
        searchInput.addEventListener('keyup', filtrarTabla);

        // Limpiar búsqueda al presionar ESC
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                filtrarTabla();
                this.blur();
            }
        });

        // Inicializar contador al cargar la página
        actualizarContador(totalRows, totalRows);

        // ========================================
        // MODAL DE EDICIÓN
        // ========================================
        const editarModal = document.getElementById('editarCitaModal');
        const editButtons = document.querySelectorAll('.edit-cita');
        const closeButtons = document.querySelectorAll('.close-modal');
        const editForm = document.getElementById('form-editar-cita');

        // Funcionalidad del botón editar
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                
                document.getElementById('edit-id').value = this.dataset.id;
                document.getElementById('edit-fecha').value = this.dataset.fecha;
                document.getElementById('edit-hora').value = this.dataset.hora;
                document.getElementById('edit-paciente').value = this.dataset.pacienteId;
                document.getElementById('edit-medico').value = this.dataset.medicoId;
    
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

        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                editarModal.style.display = 'none';
            });
        });
    </script>

</body>

</html>