<!-- Tabla de costos directos -->
<h2>
    <span class="toggle-button" onclick="toggleSection('costos-directos')">+</span>
    Costos Directos (Total: ${{ number_format($costosDirectos->sum('costo') + $totalCantidadDestajos, 2) }})
</h2>
<div id="costos-directos" class="hidden-section">
    <table class="obra-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nombre</th>
                <th>Costo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Materiales</td>
                <td><a href="{{ route('materiales.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Materiales')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>2</td>
                <td>
                    <span class="toggle-button" onclick="toggleSection('manoObra-options')">+</span>
                    Mano de Obra
                    <div id="manoObra-options" class="hidden-section">
                        <ul>
                            <li><a href="{{ route('manoObra.index', ['obraId' => $obra->id, 'tipo' => 'nomina']) }}">Nómina</a></li>
                            <li><a href="{{ route('destajos.index', ['obraId' => $obra->id]) }}">Destajos Con Nomina</a></li>
                            <li><a href="{{ route('destajosSinNomina.index', ['obraId' => $obra->id]) }}">Destajos Sin Nomina</a></li>
                        </ul>
                    </div>
                </td>
                <td>${{ number_format($costosDirectos->where('nombre', 'Mano de Obra')->sum('costo') + $totalCantidadDestajos, 2) }}</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Equipo de Seguridad</td>
                <td><a href="{{ route('equipoSeguridad.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Equipo de Seguridad')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>4</td>
                <td>Herramienta Menor</td>
                <td><a href="{{ route('herramientaMenor.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Herramienta Menor')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>5</td>
                <td>Maquinaria Menor</td>
                <td><a href="{{ route('maquinariaMenor.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Maquinaria Menor')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>6</td>
                <td>Maquinaria Mayor</td>
                <td><a href="{{ route('maquinariaMayor.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Maquinaria Mayor')->first())->costo ?? 0.00, 2) }}</a></td>
              
            </tr>
            <tr>
                <td>7</td>
                <td>Renta de Maquinaria</td>
                <td><a href="{{ route('rentaMaquinaria.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Renta de Maquinaria')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>8</td>
                <td>Limpieza</td>
                <td><a href="{{ route('limpieza.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Limpieza')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>9</td>
                <td>Cimbras</td>
                <td><a href="{{ route('cimbras.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Cimbras')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>10</td>
                <td>Acarreos</td>
                <td><a href="{{ route('acarreos.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Acarreos')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>11</td>
                <td>Comidas</td>
                <td><a href="{{ route('comidas.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Comidas')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>12</td>
                <td>Trámites</td>
                <td><a href="{{ route('tramites.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Trámites')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    function toggleSection(sectionId) {
        var section = document.getElementById(sectionId);
        if (section) {
            section.classList.toggle('hidden-section');
        }
    }
</script>

<style>
    .hidden-section {
        display: none;
    }
    .toggle-button {
        cursor: pointer;
        font-weight: bold;
        margin-right: 5px;
    }
    ul {
        list-style-type: none;
        padding: 0;
        margin: 5px 0 0 15px;
    }
    ul li {
        margin: 5px 0;
    }
</style>
