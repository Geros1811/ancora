@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Fotos del Cliente</h1>
        <link rel="stylesheet" href="{{ asset('css/cliente_fotos.css') }}">

        <div style="display: flex; justify-content: center; align-items: center;">
            <a href="{{ route('cliente_fotos.index', ['obraId' => $obra->id, 'month' => $mesAnterior->month, 'year' => $mesAnterior->year]) }}"><</a>
            <h2>Calendario de {{ ucfirst($mesActual) }}</h2>
            <a href="{{ route('cliente_fotos.index', ['obraId' => $obra->id, 'month' => $mesSiguiente->month, 'year' => $mesSiguiente->year]) }}">></a>
        </div>

        <div class="calendar-container">
            <table class="calendar">
                <thead>
                    <tr>
                        <th>Dom</th><th>Lun</th><th>Mar</th><th>Mié</th><th>Jue</th><th>Vie</th><th>Sáb</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php
                        $dia = 1;
                        // Espacios vacíos antes del primer día del mes
                        for ($i = 0; $i < $diaInicioSemana; $i++) {
                            echo '<td></td>';
                        }
                        
                        while ($dia <= $diasEnMes) {
                            if (($dia + $diaInicioSemana - 1) % 7 == 0) {
                                echo '</tr><tr>'; // Nueva fila para la semana
                            }
                            
                            // Buscar fotos para el día actual
                            $fechaDia = $primerDia->copy()->addDays($dia - 1)->toDateString();
                            $fotoDelDia = $fotos->where('fecha', $fechaDia);
                            
                            echo '<td class="day" data-fecha="' . $fechaDia . '">
                                    <span class="day-number">' . $dia . '</span>';
                            
                            if ($fotoDelDia->isEmpty()) {
                                echo '<div class="add-photo" onclick="openAddPhotoModal(\'' . $fechaDia . '\')">Agregar Foto</div>';
                            } else {
                                foreach ($fotoDelDia as $foto) {
                                    echo '<img src="' . asset('storage/' . $foto->imagen) . '" width="50" onclick="openModal(\'' . asset('storage/' . $foto->imagen) . '\')">
                                          <p>' . $foto->titulo . '</p>
                                          <p>' . $foto->comentario . '</p>';
                                }
                            }

                            echo '</td>';
                            $dia++;
                        }
                        ?>
                    </tr>
                </tbody>
            </table>
    </div>

<div id="modalFoto" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Agregar Foto</h2>
        <form action="{{ route('cliente_fotos.store', ['obraId' => $obra->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="obra_id" value="{{ $obra->id }}">
            <input type="hidden" name="fecha" id="fechaSeleccionada">
            <div>
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo">
            </div>
            <div>
                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen">
            </div>
            <div>
                <label for="comentario">Observación:</label>
                <textarea id="comentario" name="comentario"></textarea>
            </div>
            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<!-- Modal for Enlarge Image -->
<div id="enlargeImgModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="img01">
</div>

@endsection

<script>
function openAddPhotoModal(fecha) {
    document.getElementById('fechaSeleccionada').value = fecha;
    document.getElementById('modalFoto').style.display = "block";
}

function openModal(imgSrc) {
    document.getElementById('enlargeImgModal').style.display = "block";
    document.getElementById('img01').src = imgSrc;
}

function closeModal() {
    document.getElementById('enlargeImgModal').style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById("modalFoto");
    let closeBtn = document.querySelector(".close");

    closeBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
</script>
