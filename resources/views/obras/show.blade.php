@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="dashboard-container">
        <h1>Detalles de la Obra: {{ $obra->nombre }}</h1>

        @section('head')
        <link rel="stylesheet" href="{{ asset('css/obra-details.css') }}">
    @endsection

  <a href="{{ route('gastos_rapidos.create', ['obraId' => $obra->id]) }}" class="btn-flotante">
    +
  </a>

  <style>
    .btn-flotante {
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 24px;
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
      z-index: 1000;
    }

    .btn-flotante:hover {
      background-color: #0056b3;
    }
  </style>
    

        <!-- Sección de información general -->
        <div class="obra-info">
            <p><strong>Presupuesto:</strong> ${{ number_format($obra->presupuesto, 2) }}</p>
            <p><strong>Metros Cuadrados:</strong> {{ $obra->metros_cuadrados }} MT2</p>
            <p><strong>Cliente:</strong> {{ $obra->cliente }}</p>
            <p><strong>Fecha de Inicio:</strong> {{ $obra->fecha_inicio }}</p>
            <p><strong>Fecha de Término:</strong> {{ $obra->fecha_termino }}</p>
            <p><strong>Residente de Obra:</strong> {{ $obra->residente }}</p>
            <p><strong>Ubicación:</strong> {{ $obra->ubicacion }}</p>
            <p><strong>Descripción:</strong> {{ $obra->descripcion }}</p>
        </div>

                <!-- Incluir las vistas parciales -->
                @include('obra.calendario-pagos')
                @include('obra.gastos-generales')
                @include('obra.costos-directos', ['costosDirectos' => $costosDirectos])
                @include('obra.costos-indirectos', ['costosIndirectos' => $costosIndirectos])
                @include('obra.pagos-administrativos', ['obra' => $obra])
            </div>

        <!-- Botón para crear gráfica -->
        <div style="text-align: center; margin-top: 20px;">
            <button onclick="crearGrafica()">Crear Gráfica</button>
        </div>

        <style>
            #grafica-container {
                text-align: center;
                margin-top: 50px;
                position: relative;
            }
            #grafica {
                max-width: 500px;
                max-height: 500px;
                width: 100%;
                height: auto;
                margin: 0 auto;
                cursor: pointer; /* Añadir cursor de puntero */
            }
            #fullscreen-icon {
                position: absolute;
                bottom: -30px;
                right: 50%;
                transform: translateX(50%);
                cursor: pointer;
                font-size: 24px;
            }
            .fullscreen #grafica {
                width: 100vw;
                height: 100vh;
                object-fit: contain; /* Ajustar la gráfica sin estirarla */
            }
            .fullscreen #grafica-container {
                width: 100vw;
                height: 100vh;
                background: none; /* Sin fondo */
            }
            .fullscreen #fullscreen-icon {
                bottom: 10px;
                right: 10px;
                transform: none;
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                cargarCalendarioPagos();
            });
        
            function cargarCalendarioPagos() {
                fetch(`/obras/{{ $obra->id }}/calendario-pagos`)
                    .then(response => response.json())
                    .then(data => {
                        let tableBody = document.getElementById("calendario-pagos-body");
                        tableBody.innerHTML = ""; // Limpiar la tabla antes de agregar los datos
        
                        data.forEach(pago => {
                            let newRow = tableBody.insertRow();
        
                            let cellConcepto = newRow.insertCell(0);
                            cellConcepto.innerHTML = `<input type="text" value="${pago.concepto}" class="editable">`;
        
                            let cellFechaPago = newRow.insertCell(1);
                            cellFechaPago.innerHTML = `<input type="date" value="${pago.fecha_pago}" class="editable">`;
        
                            let cellPago = newRow.insertCell(2);
                            cellPago.innerHTML = `<input type="number" value="${pago.pago}" class="editable" oninput="updateAcumulado()" onblur="formatCurrency(this)">`;
        
                            let cellAcumulado = newRow.insertCell(3);
                            cellAcumulado.innerHTML = `<input type="text" value="${formatCurrencyValue(pago.acumulado)}" class="editable" disabled>`;
        
                            let cellAccion = newRow.insertCell(4);
                            cellAccion.innerHTML = `<span onclick="toggleLock(this)" class="lock-icon" style="color: ${pago.bloqueado ? 'green' : 'black'};">${pago.bloqueado ? '🔒' : '🔓'}</span>`;
        
                            if (pago.bloqueado) {
                                bloquearFila(newRow);
                            }
                        });
        
                        updateAcumulado(); // Actualizar el acumulado después de cargar los datos
                    })
                    .catch(error => {
                        console.error('Error al cargar el calendario de pagos:', error);
                    });
            }
        
            function bloquearFila(row) {
                const inputs = row.querySelectorAll("input");
                inputs.forEach(input => input.disabled = true);
                row.style.backgroundColor = "#d4edda"; // Cambiar color de fondo a verde claro
            }
        
            // Función para actualizar el acumulado
            function updateAcumulado() {
                let rows = document.getElementById("calendario-pagos-body").rows;
                let total = 0;
        
                for (let i = 0; i < rows.length; i++) {
                    let paymentCell = rows[i].cells[2].getElementsByTagName("input")[0]; // Pago
                    let acumuladoCell = rows[i].cells[3].getElementsByTagName("input")[0]; // Acumulado
        
                    // Convertir el valor de pago a número (sin formato)
                    let paymentValue = parseFloat(paymentCell.value.replace(/,/g, "")) || 0;
        
                    // Sumar al acumulado total
                    total += paymentValue;
        
                    // Actualizar el acumulado formateado (sin permitir edición)
                    acumuladoCell.value = formatCurrencyValue(total);
                }
        
                // Actualizar el total formateado en el título
                document.getElementById("total-pago").textContent = formatCurrencyValue(total) + " MXN";
            }
        
            // Función para formatear a moneda MXN solo al perder el foco
            function formatCurrency(input) {
                // Formatear solo cuando el usuario termine de escribir
                let value = parseFloat(input.value.replace(/,/g, "")) || 0;
                if (input.value !== "") {
                    input.value = value.toFixed(2); // Dejar sin el símbolo de moneda para celdas editables
                }
                updateAcumulado(); // Recalcular el acumulado
            }
        
            // Función para formatear un número como moneda para celdas no editables
            function formatCurrencyValue(value) {
                return new Intl.NumberFormat("es-MX", { style: "currency", currency: "MXN" }).format(value).replace("$", "");
            }
        
            // Función para agregar una nueva fila
            function addRow() {
                var table = document.getElementById("calendario-pagos-body");
                var newRow = table.insertRow();
        
                for (let i = 0; i < 5; i++) {
                    var cell = newRow.insertCell(i);
                    if (i === 0) {
                        cell.innerHTML = '<input type="text" value="Nuevo Concepto" class="editable">';
                    } else if (i === 1) {
                        cell.innerHTML = '<input type="date" class="editable">';
                    } else if (i === 2) {
                        cell.innerHTML = '<input type="number" value="0.00" class="editable" onblur="formatCurrency(this)">';
                    } else if (i === 3) {
                        cell.innerHTML = '<input type="text" value="$0.00" class="editable" disabled>';
                    } else if (i === 4) {
                        cell.innerHTML = '<span onclick="toggleLock(this)" class="lock-icon" style="color: green;">🔓</span>';
                    }
                }
        
                // Actualizar el acumulado al agregar una fila
                updateAcumulado();
            }
        
            // Función para activar/desactivar el bloqueo
            function toggleLock(lockIcon) {
                const row = lockIcon.closest("tr"); // Fila correspondiente al candado
                const inputs = row.querySelectorAll("input"); // Todos los campos de entrada dentro de la fila
        
                if (lockIcon.innerHTML === "🔓") {
                    // Bloquear todos los campos de la fila y ponerla verde
                    inputs.forEach(input => input.disabled = true);
                    row.style.backgroundColor = "#d4edda"; // Cambiar color de fondo a verde claro
                    lockIcon.style.color = "green";
                    lockIcon.innerHTML = "🔒"; // Cambiar icono a "bloqueado"
                    
                    // Guardar cambios automáticamente
                    guardarCambios();
                } else if (lockIcon.innerHTML === "🔒") {
                    // Solicitar contraseña
                    const password = prompt("Introduce la contraseña para desbloquear:");
        
                    // Validar la contraseña con AJAX
                    fetch('/validar-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ password: password })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Desbloquear todos los campos de la fila y restaurar color
                            inputs.forEach(input => input.disabled = false);
                            row.style.backgroundColor = ""; // Restaurar color de fondo original
                            lockIcon.style.color = ""; // Restaurar color original del icono
                            lockIcon.innerHTML = "🔓"; // Cambiar icono a "desbloqueado"
                        } else {
                            alert("Contraseña incorrecta."); // Mostrar mensaje de error
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("Hubo un problema al validar la contraseña.");
                    });
                }
            }
        
            // Función para mostrar/ocultar secciones
            function toggleSection(sectionId) {
                const section = document.getElementById(sectionId);
                const button = section.previousElementSibling.querySelector('.toggle-button');
                if (section.classList.contains('hidden-section')) {
                    section.classList.remove('hidden-section');
                    button.textContent = '-';
                } else {
                    section.classList.add('hidden-section');
                    button.textContent = '+';
                }
            }
        
            // Función para guardar los cambios
            function guardarCambios() {
                let rows = document.getElementById("calendario-pagos-body").rows;
                let data = [];
                let obraId = {{ $obra->id }}; // Obtener el ID de la obra
        
                for (let i = 0; i < rows.length; i++) {
                    let concepto = rows[i].cells[0].getElementsByTagName("input")[0].value;
                    let fecha_pago = rows[i].cells[1].getElementsByTagName("input")[0].value; // Cambiar a fecha_pago
                    let pago = parseFloat(rows[i].cells[2].getElementsByTagName("input")[0].value.replace(/,/g, "")) || 0;
                    let acumulado = parseFloat(rows[i].cells[3].getElementsByTagName("input")[0].value.replace(/,/g, "")) || 0;
                    let bloqueado = rows[i].cells[4].getElementsByTagName("span")[0].innerHTML === "🔒";
        
                    data.push({
                        concepto: concepto,
                        fecha_pago: fecha_pago, // Cambiar a fecha_pago
                        pago: pago,
                        acumulado: acumulado,
                        bloqueado: bloqueado
                    });
                }
        
                fetch('/guardar-cambios', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ pagos: data, obra_id: obraId }) // Enviar el ID de la obra
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Cambios guardados exitosamente.");
                    } else {
                        alert("Hubo un problema al guardar los cambios.");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Hubo un problema al guardar los cambios.");
                });
            }

            function crearGrafica() {
    let presupuesto = parseFloat("{{ $obra->presupuesto }}");
    let costosDirectos = {
        'Materiales': parseFloat("{{ optional($costosDirectos->where('nombre', 'Materiales')->first())->costo ?? 0.00 }}"),
        'Mano de Obra': parseFloat("{{ $costosDirectos->where('nombre', 'Mano de Obra')->sum('costo') + $totalCantidadDestajos }}"),
        'Equipo de Seguridad': parseFloat("{{ optional($costosDirectos->where('nombre', 'Equipo de Seguridad')->first())->costo ?? 0.00 }}"),
        'Herramienta Menor': parseFloat("{{ optional($costosDirectos->where('nombre', 'Herramienta Menor')->first())->costo ?? 0.00 }}"),
        'Maquinaria Menor': parseFloat("{{ optional($costosDirectos->where('nombre', 'Maquinaria Menor')->first())->costo ?? 0.00 }}"),
        'Limpieza': parseFloat("{{ optional($costosDirectos->where('nombre', 'Limpieza')->first())->costo ?? 0.00 }}"),
        'Maquinaria Mayor': parseFloat("{{ optional($costosDirectos->where('nombre', 'Maquinaria Mayor')->first())->costo ?? 0.00 }}"),
        'Renta de Maquinaria': parseFloat("{{ optional($costosDirectos->where('nombre', 'Renta de Maquinaria')->first())->costo ?? 0.00 }}"),
        'Cimbras': parseFloat("{{ optional($costosDirectos->where('nombre', 'Cimbras')->first())->costo ?? 0.00 }}"),
        'Acarreos': parseFloat("{{ optional($costosDirectos->where('nombre', 'Acarreos')->first())->costo ?? 0.00 }}"),
        'Comidas': parseFloat("{{ optional($costosDirectos->where('nombre', 'Comidas')->first())->costo ?? 0.00 }}"),
        'Trámites': parseFloat("{{ optional($costosDirectos->where('nombre', 'Trámites')->first())->costo ?? 0.00 }}")
    };
    let costosIndirectos = {
        'Papelería': parseFloat("{{ optional($costosIndirectos->where('nombre', 'Papelería')->first())->costo ?? 0.00 }}"),
        'Gasolina': parseFloat("{{ optional($costosIndirectos->where('nombre', 'Gasolina')->first())->costo ?? 0.00 }}"),
        'Rentas': parseFloat("{{ optional($costosIndirectos->where('nombre', 'Rentas')->first())->costo ?? 0.00 }}"),
        'Utilidades': parseFloat("{{ optional($costosIndirectos->where('nombre', 'Utilidades')->first())->costo ?? 0.00 }}")
    };
    let totalCostosDirectos = Object.values(costosDirectos).reduce((a, b) => a + b, 0);
    let totalCostosIndirectos = Object.values(costosIndirectos).reduce((a, b) => a + b, 0);
    let totalGastos = totalCostosDirectos + totalCostosIndirectos;
    let utilidadRemanente = presupuesto - totalGastos;

    let data = {
        labels: [...Object.keys(costosDirectos), ...Object.keys(costosIndirectos), 'Utilidad Remanente'],
        datasets: [{
            data: [...Object.values(costosDirectos), ...Object.values(costosIndirectos), utilidadRemanente],
            backgroundColor: [
                '#FF0000', // Materiales
                '#0000FF', // Mano de Obra
                '#FFFF00', // Equipo de Seguridad
                '#FFA500', // Herramienta Menor
                '#FFC0CB', // Maquinaria Menor
                '#800080', // Limpieza
                '#A52A2A', // Maquinaria Mayor
                '#FFFFFF', // Renta de Maquinaria
                '#000000', // Cimbras
                '#808080', // Acarreos
                '#40E0D0', // Comidas (o el color que desees)
                '#FFD700', // Trámites
                '#C0C0C0', // Papelería
                '#F5F5DC', // Gasolina
                '#FF00FF', // Rentas
                '#00CED1', // Utilidades → color turquesa
                '#008000'  // Utilidad Remanente → color verde
            ]
        }]
    };

    // Aquí seguiría el resto del código para crear la gráfica...


        
                let config = {
                    type: 'pie',
                    data: data,
                    options: {
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        let total = tooltipItem.dataset.data.reduce((a, b) => a + b, 0);
                                        let value = tooltipItem.raw;
                                        let percentage = ((value / total) * 100).toFixed(2);
                                        return `${tooltipItem.label}: ${value.toFixed(2)} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                };
        
                let graficaContainer = document.createElement('div');
                graficaContainer.id = 'grafica-container';
                graficaContainer.innerHTML = '<canvas id="grafica"></canvas><div id="fullscreen-icon" onclick="toggleFullscreen()">🔍</div>';
                document.body.appendChild(graficaContainer);
        
                let ctx = document.getElementById('grafica').getContext('2d');
                new Chart(ctx, config);
            }

            function toggleFullscreen() {
                let graficaContainer = document.getElementById('grafica-container');
                let grafica = document.getElementById('grafica');
                let fullscreenIcon = document.getElementById('fullscreen-icon');
                if (!graficaContainer.classList.contains('fullscreen')) {
                    graficaContainer.classList.add('fullscreen');
                    grafica.style.width = '100vw';
                    grafica.style.height = '100vh';
                    grafica.style.maxWidth = 'none';
                    grafica.style.maxHeight = 'none';
                    fullscreenIcon.innerHTML = '❌'; // Cambiar icono a "cerrar"
                } else {
                    graficaContainer.classList.remove('fullscreen');
                    grafica.style.width = '';
                    grafica.style.height = '';
                    grafica.style.maxWidth = '500px';
                    grafica.style.maxHeight = '500px';
                    fullscreenIcon.innerHTML = '🔍'; // Cambiar icono a "fullscreen"
                }
            }
        </script>
@endsection
