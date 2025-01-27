<!-- Tabla de costos directos -->
<h2>
    <span class="toggle-button" onclick="toggleSection('costos-directos')">+</span>
    Costos Directos (Total: ${{ number_format($costosDirectos->sum('costo'), 2) }})
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
                <td>Mano de Obra</td>
                <td><a href="{{ route('manoObra.index', ['obraId' => $obra->id]) }}">${{ number_format($costosDirectos->where('nombre', 'Mano de Obra')->sum('costo'), 2) }}</a></td>
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
                <td>Limpieza</td>
                <td><a href="{{ route('limpieza.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Limpieza')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>7</td>
                <td>Maquinaria Mayor</td>
                <td><a href="{{ route('maquinariaMayor.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Maquinaria Mayor')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>8</td>
                <td>Cimbras</td>
                <td><a href="{{ route('cimbras.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Cimbras')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>9</td>
                <td>Acarreos</td>
                <td><a href="{{ route('acarreos.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Acarreos')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>10</td>
                <td>Comidas</td>
                <td><a href="{{ route('comidas.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Comidas')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>11</td>
                <td>Trámites</td>
                <td><a href="{{ route('tramites.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosDirectos->where('nombre', 'Trámites')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
        </tbody>
    </table>
</div>