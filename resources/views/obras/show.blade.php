@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="dashboard-container">
        <h1>Detalles de la Obra: {{ $obra->nombre }}</h1>
        @if(Auth::check() && Auth::user()->hasRole('arquitecto'))
            <a href="#" id="notification-button" style="float: right;" disabled>
                <i class="fas fa-bell" style="font-size: 24px;"></i>
                @if(Auth::user()->notifications()->whereNull('read_at')->count() > 0)
                    <span class="notification-badge"></span>
                @endif
            </a>
        @endif

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
    .notification-badge {
        position: relative;
        top: -10px;
        right: -10px;
        display: inline-block;
        width: 10px;
        height: 10px;
        background-color: red;
        border-radius: 50%;
        border: 1px solid white;
    }
  </style>
    

        <!-- Sección de información general -->
        <div class="obra-info">
            @if(Auth::user()->role == 'arquitecto' || Auth::user()->role == 'cliente')
                <p><strong>Presupuesto:</strong> ${{ number_format($obra->presupuesto, 2) }}</p>
                <p><strong>Metros Cuadrados:</strong> {{ $obra->metros_cuadrados }} MT2</p>
                <p><strong>Cliente:</strong> {{ $cliente->name }}</p>
            @endif
            @if(Auth::user()->role != 'residente' && $obra->fecha_inicio)
                <p><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($obra->fecha_inicio)->format('d/m/Y') }}</p>
            @endif
            @if(Auth::user()->role != 'residente' && $obra->fecha_termino)
                <p><strong>Fecha de Término:</strong> {{ \Carbon\Carbon::parse($obra->fecha_termino)->format('d/m/Y') }}</p>
            @endif
            @if(Auth::user()->role != 'residente')
               
                @if(isset($residente))
                    <p><strong>Residente de Obra:</strong> {{ $residente->name }}</p>
                @else
                    <p><strong>Residente de Obra:</strong> No asignado</p>
                @endif
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
                bottom: 30px;
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
        
           


            // Función para crear la gráfica
        function crearGrafica() {
        let presupuesto = parseFloat("{{ $obra->presupuesto }}");
        let costosDirectos = {
        'Materiales': parseFloat("{{ optional($costosDirectos->where('nombre', 'Materiales')->first())->costo ?? 0.00 }}"),
        'Mano de Obra': parseFloat("{{ $costosDirectos->where('nombre', 'Mano de Obra')->sum('costo') + $totalCantidadDestajos + ($totalPagosDestajosSinNomina ?? 0) }}"),
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
            '#008000'
        ]
    }]
};

let utilidadRemanenteInicial = 0; // Inicialmente oculto
data.datasets[0].data[data.labels.indexOf('Utilidad Remanente')] = utilidadRemanenteInicial;

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
                let myChart = new Chart(ctx, config);

                // Add click event listener to the chart
                document.getElementById('grafica').onclick = function() {
                    if (utilidadRemanenteInicial === 0) {
                        // Reveal utilidadRemanente
                        data.datasets[0].data[data.labels.indexOf('Utilidad Remanente')] = utilidadRemanente;
                        myChart.update(); // Update the chart
                        utilidadRemanenteInicial = utilidadRemanente; // Update the flag
                    }
                };
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

        <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                @include('notifications.index', ['obra' => $obra])
            </div>
        </div>
    </div>
</div>

<style>
    /* Hacer que el modal cubra toda la altura de la pantalla y esté alineado a la derecha */
    #notificationModal .modal-dialog {
        position: fixed;
        top: 0;
        right: 0;
        height: 100vh;
        max-width: 350px;
        width: 100%;
        margin: 0;
        display: flex;
        align-items: stretch;
    }

    #notificationModal .modal-content {
        height: 100vh;
        border: none;
        box-shadow: -5px 0 10px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
    }

    #notificationModal .modal-body {
        flex-grow: 1;
        overflow-y: auto;
        padding: 15px;
    }

    /* Animación para deslizar desde la derecha */
    #notificationModal.fade .modal-dialog {
        transform: translateX(100%);
        transition: transform 0.3s ease-out;
    }

    #notificationModal.show .modal-dialog {
        transform: translateX(0);
    }

    #notificationModal .modal-header .close {
        font-size: 1.5rem; /* Increase the size of the close button */
    }

    #notificationModal .modal-dialog {
        background-color: white; /* Ensure a solid background */
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#notification-button').prop('disabled', false);

        $('#notification-button').on('click', function(event) {
            event.preventDefault();
            $('#notificationModal').modal('show');
            
            // Mark all notifications as read when the modal is opened
            $.ajax({
                url: "/marcar-todas-notificaciones-leidas",
                type: "POST",
                data: {
                    obra_id: {{ $obra->id }},
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // Hide the red dot from the notification button
                        $('#notification-button .notification-badge').hide();
                    }
                },
                error: function() {
                    alert("Hubo un error al marcar las notificaciones como leídas.");
                }
            });
        });

        $('.close').on('click', function() {
            $('#notificationModal').modal('hide');
        });
    });
</script>

@endsection
