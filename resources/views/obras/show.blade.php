@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="dashboard-container">
        <h1>Detalles de la Obra: {{ $obra->nombre }}</h1>

        @section('head')
        <link rel="stylesheet" href="{{ asset('css/obra-details.css') }}">
    @endsection

  @if(Auth::user()->role != 'cliente')
    <a href="{{ route('gastos_rapidos.create', ['obraId' => $obra->id]) }}" class="btn-flotante">
      +
    </a>
  @endif

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
            @if(Auth::user()->role == 'arquitecto' || Auth::user()->role == 'cliente')
                <p><strong>Presupuesto:</strong> ${{ number_format($obra->presupuesto, 2) }}</p>
                <p><strong>Metros Cuadrados:</strong> {{ $obra->metros_cuadrados }} MT2</p>
                <p><strong>Cliente:</strong> {{ $obra->cliente }}</p>
            @endif
            @if(Auth::user()->role != 'residente')
                <p><strong>Fecha de Inicio:</strong> {{ $obra->fecha_inicio }}</p>
                <p><strong>Fecha de Término:</strong> {{ $obra->fecha_termino }}</p>
                <p><strong>Residente de Obra:</strong> {{ $obra->residente }}</p>
                <p><strong>Ubicación:</strong> {{ $obra->ubicacion }}</p>
                <p><strong>Descripción:</strong> {{ $obra->descripcion }}</p>
            @endif
        </div>

                <!-- Incluir las vistas parciales -->
                @if(Auth::user()->role == 'arquitecto' || Auth::user()->role == 'cliente')
                    @include('obra.calendario-pagos')
                    @include('obra.gastos-generales')
                @endif
                @if(Auth::user()->role == 'arquitecto')
                    @include('obra.costos-directos', ['costosDirectos' => $costosDirectos])
                    @include('obra.costos-indirectos', ['costosIndirectos' => $costosIndirectos])
                    @include('obra.pagos-administrativos', ['pagosAdministrativos' => $pagosAdministrativos])
                @endif

            </div>

        <!-- Botón para crear gráfica -->
        @if(Auth::user()->role == 'arquitecto')
            <div style="text-align: center; margin-top: 20px;">
                <button onclick="crearGrafica()">Crear Gráfica</button>
            </div>
        @endif

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
                @if(Auth::user()->role == 'cliente')
                    alert('No tienes permiso para desbloquear este elemento.');
                    return;
                @endif
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
        'Utilidades': parseFloat("{{ optional($costosIndirectos->where('nombre', 'Utilidades')->first())->costo ?? 0.00 }}"),
        'Sueldo Residente': parseFloat("{{ optional($costosIndirectos->where('nombre', 'Sueldo Residente')->first())->costo ?? 0.00 }}")
    };

    let pagosAdministrativosData = {};
    @php
        $pagosAdministrativosNombres = ['IMSS', 'Contador', 'IVA', 'Otros Pagos Administrativos'];
        foreach ($pagosAdministrativosNombres as $nombre) {
            $sessionKey = 'pagos_administrativos.' . $nombre;
            $costo = optional($pagosAdministrativos->where('nombre', $nombre)->first())->costo ?? 0.00;
            echo "pagosAdministrativosData['$nombre'] = " . (Session::get($sessionKey) !== false ? "parseFloat('$costo')" : "0.00") . ";\n";
        }
    @endphp

    let totalCostosDirectos = Object.values(costosDirectos).reduce((a, b) => a + b, 0);
    let totalCostosIndirectos = Object.values(costosIndirectos).reduce((a, b) => a + b, 0);
    let totalPagosAdministrativos = Object.values(pagosAdministrativosData).reduce((a, b) => a + b, 0);
    let totalGastos = totalCostosDirectos + totalCostosIndirectos + totalPagosAdministrativos;
    let utilidadRemanente = presupuesto - totalGastos;

    let data = {
    labels: [...Object.keys(costosDirectos), ...Object.keys(costosIndirectos), ...Object.keys(pagosAdministrativosData), 'Utilidad Remanente'],
    datasets: [{
        data: [...Object.values(costosDirectos), ...Object.values(costosIndirectos), ...Object.values(pagosAdministrativosData), utilidadRemanente],
        backgroundColor: [
            '#FF0000', // Materiales → Rojo
            '#0000FF', // Mano de Obra → Azul
            '#FFFF00', // Equipo de Seguridad → Amarillo
            '#FF7F00', // Herramienta Menor → Naranja fuerte
            '#FF69B4', // Maquinaria Menor → Rosa fuerte
            '#800080', // Limpieza → Morado oscuro
            '#8B4513', // Maquinaria Mayor → Marrón oscuro
            '#FFFFFF', // Renta de Maquinaria → Blanco
            '#000000', // Cimbras → Negro
            '#808080', // Acarreos → Gris
            '#00FFFF', // Comidas → Cian
            '#FFD700', // Trámites → Dorado
            '#C0C0C0', // Papelería → Plata
            '#008080', // Gasolina → Verde azulado
            '#FF1493', // Rentas → Rosa fuerte neón
            '#20B2AA', // Utilidades → Verde agua
            '#006400', // Sueldo Residente → Verde oscuro
            '#4682B4', // IMSS → Azul acero
            '#DC143C', // Contador → Rojo carmesí
            '#1E90FF', // IVA → Azul neón
            '#9400D3', // Otros Pagos Administrativos → Púrpura intenso
            '#008000'  // Utilidad Remanente → Verde puro ✅ (aquí está el cambio)
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
                graficaContainer.innerHTML = '<canvas id="grafica"></canvas><div id="fullscreen-icon" onclick="openModal()">🔍</div>';
                document.body.appendChild(graficaContainer);
        
                let ctx = document.getElementById('grafica').getContext('2d');
                new Chart(ctx, config);
            }

            function openModal() {
                let modal = document.createElement('div');
                modal.id = 'graficaModal';
                modal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                `;

                let modalContent = document.createElement('div');
                modalContent.style.cssText = `
                    background-color: white;
                    padding: 20px;
                    border-radius: 5px;
                    width: 90vw;
                    height: 90vh;
                    max-width: 90vw;
                    max-height: 90vh;
                    overflow: auto;
                    position: relative;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                `;

                let canvasCopy = document.getElementById('grafica').cloneNode(true);
                canvasCopy.id = 'graficaModalCanvas';
                canvasCopy.style.maxWidth = '100%';
                canvasCopy.style.maxHeight = '100%';
                modalContent.appendChild(canvasCopy);

                let closeButton = document.createElement('button');
                closeButton.innerText = 'X';
                closeButton.style.cssText = `
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    font-size: 20px;
                    border: none;
                    background-color: transparent;
                    cursor: pointer;
                `;
                closeButton.onclick = function() {
                    document.body.removeChild(modal);
                };
                modalContent.appendChild(closeButton);

                modal.appendChild(modalContent);
                document.body.appendChild(modal);

                // Redraw the chart on the cloned canvas
                let ctx = canvasCopy.getContext('2d');
                let originalChart = Chart.getChart('grafica'); // Get the chart instance
                if (originalChart) {
                    new Chart(ctx, originalChart.config); // Redraw the chart
                }
            }
        </script>
@endsection
