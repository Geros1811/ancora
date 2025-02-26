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
            @foreach ($pagosAdministrativos as $pago)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $pago['nombre'] }}</td>
                <td><a href="{{ $pago['link'] }}">${{ number_format($pago['costo'], 2) }}</a></td>
            </tr>
            @endforeach
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