<h2>
    <span class="toggle-button" onclick="toggleSection('pagos-administrativos')">+</span>
    Pagos Administrativos (Total: ${{ number_format($pagosAdministrativos->sum('costo'), 2) }})
</h2>
<div id="pagos-administrativos" class="hidden-section">
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
                <td>Sueldo Residente</td>
                <td><a href="{{ route('sueldo-residente.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($pagosAdministrativos->where('nombre', 'Sueldo Residente')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>2</td>
                <td>IMSS</td>
                <td><a href="{{ route('imss.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($pagosAdministrativos->where('nombre', 'IMSS')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Contador</td>
                <td><a href="{{ route('contador.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($pagosAdministrativos->where('nombre', 'Contador')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>4</td>
                <td>IVA</td>
                <td><a href="{{ route('iva.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($pagosAdministrativos->where('nombre', 'IVA')->first())->costo ?? 0.00, 2) }}</a></td>
            </tr>
            <tr>
                <td>5</td>
                <td>Otros Pagos Administrativos</td>
                <td><a href="{{ route('otros_pagos_administrativos.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($pagosAdministrativos->where('nombre', 'Otros Pagos Administrativos')->first())->costo ?? 0.00, 2) }}</a></td>
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
</style>
