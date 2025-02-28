@php
    $costosIndirectos = $costosIndirectos ?? collect();
@endphp

<h2>
    <span class="toggle-button" onclick="toggleSection('costos-indirectos')">+</span>
    Costos Indirectos (Total: ${{ number_format($costosIndirectos->sum('costo'), 2) }})
</h2>
<div id="costos-indirectos" class="hidden-section">
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
                <td>Papelería</td>
                <td><a href="{{ route('papeleria.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosIndirectos->where('nombre', 'Papelería')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Gasolina</td>
                <td><a href="{{ route('gasolina.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosIndirectos->where('nombre', 'Gasolina')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Rentas</td>
                <td><a href="{{ route('rentas.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosIndirectos->where('nombre', 'Rentas')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>4</td>
                <td>Utilidades</td>
                <td><a href="{{ route('utilidades.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($costosIndirectos->where('nombre', 'Utilidades')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
<tr>
                <td>5</td>
                <td>Sueldo Residente</td>
                <td><a href="{{ route('sueldo-residente.index', ['obraId' => $obra->id]) }}">${{ number_format($costosIndirectos->where('nombre', 'Sueldo Residente')->first()->costo ?? 0.00, 2) }}</a></td>
            </tr>
        </tbody>
    </table>
</div>
