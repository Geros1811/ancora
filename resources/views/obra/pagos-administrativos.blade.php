<h2>
    <span class="toggle-button" onclick="toggleSection('pagos-administrativos')">+</span>
    Pagos Administrativos (Total: $0.00)
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
                <td><a href="{{ route('sueldo-residente.index', ['obraId' => $obra->id]) }}"> $0.00</a></td>
            </tr>
            <tr>
                <td>2</td>
                <td>IMSS</td>
                <td><a href="{{ route('imss.index', ['obraId' => $obra->id]) }}"> $0.00</a></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Contador</td>
                <td><a href="{{ route('contador.index', ['obraId' => $obra->id]) }}"> $0.00</a></td>
            </tr>
            <tr>
                <td>4</td>
                <td>IVA</td>
                <td><a href="{{ route('iva.index', ['obraId' => $obra->id]) }}"> $0.00</a></td>
            </tr>
        </tbody>
    </table>
</div>
