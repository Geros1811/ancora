<h2>
    <span class="toggle-button" onclick="toggleSection('pagos-administrativos')">+</span>
    Pagos Administrativos (Total Real: $<span id="total-pagos">{{ number_format($pagosAdministrativos->sum('costo'), 2) }}</span>)
    <a href="{{ route('pagosAdministrativos.consolidatedPdf', ['obraId' => $obraId ?? $obra->id]) }}" class="btn btn-primary" style="margin-left: 10px; margin-bottom: 10px;" target="_blank">
        PDF Consolidado <i class="fas fa-file-pdf" style="margin-left: 5px;"></i>
    </a>
</h2>
<div id="pagos-administrativos" class="hidden-section">
    <table class="obra-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nombre</th>
                <th>Costo</th>
                <th>Incluir</th>
            </tr>
        </thead>
        <tbody>

<tr>
    <td>1</td>
    <td>IMSS</td>
    <td><a href="{{ route('imss.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($pagosAdministrativos->where('nombre', 'IMSS')->first())->costo ?? 0.00, 2) }}</a></td>
    <td><span class="toggle-eye" onclick="togglePago(this, {{ optional($pagosAdministrativos->where('nombre', 'IMSS')->first())->costo ?? 0.00 }})" data-active="{{ Session::get('pagos_administrativos.IMSS') !== false ? 'true' : 'false' }}">{{ Session::get('pagos_administrativos.IMSS') !== false ? '👁️' : '🚫' }}</span></td>
</tr>
<tr>
    <td>2</td>
    <td>Contador</td>
    <td><a href="{{ route('contador.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($pagosAdministrativos->where('nombre', 'Contador')->first())->costo ?? 0.00, 2) }}</a></td>
    <td><span class="toggle-eye" onclick="togglePago(this, {{ optional($pagosAdministrativos->where('nombre', 'Contador')->first())->costo ?? 0.00 }})" data-active="{{ Session::get('pagos_administrativos.Contador') !== false ? 'true' : 'false' }}">{{ Session::get('pagos_administrativos.Contador') !== false ? '👁️' : '🚫' }}</span></td>
</tr>
<tr>
    <td>3</td>
    <td>IVA</td>
    <td><a href="{{ route('iva.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($pagosAdministrativos->where('nombre', 'IVA')->first())->costo ?? 0.00, 2) }}</a></td>
    <td><span class="toggle-eye" onclick="togglePago(this, {{ optional($pagosAdministrativos->where('nombre', 'IVA')->first())->costo ?? 0.00 }})" data-active="{{ Session::get('pagos_administrativos.IVA') !== false ? 'true' : 'false' }}">{{ Session::get('pagos_administrativos.IVA') !== false ? '👁️' : '🚫' }}</span></td>
</tr>
<tr>
    <td>4</td>
    <td>Otros Pagos Administrativos</td>
    <td><a href="{{ route('otros_pagos_administrativos.index', ['obraId' => $obra->id]) }}">${{ number_format(optional($pagosAdministrativos->where('nombre', 'Otros Pagos Administrativos')->first())->costo ?? 0.00, 2) }}</a></td>
    <td><span class="toggle-eye" onclick="togglePago(this, {{ optional($pagosAdministrativos->where('nombre', 'Otros Pagos Administrativos')->first())->costo ?? 0.00 }})" data-active="{{ Session::get('pagos_administrativos.Otros Pagos Administrativos') !== false ? 'true' : 'false' }}">{{ Session::get('pagos_administrativos.Otros Pagos Administrativos') !== false ? '👁️' : '🚫' }}</span></td>
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

    function togglePago(element, costo) {
        let totalElement = document.getElementById("total-pagos");
        let total = parseFloat(totalElement.innerText.replace(/,/g, ''));
        let isActive = element.getAttribute("data-active") === "true";

        if (isActive) {
            total -= costo;
            element.innerText = "🚫";
        } else {
            total += costo;
            element.innerText = "👁️";
        }

        element.setAttribute("data-active", !isActive);
        totalElement.innerText = total.toFixed(2);

        // Store the state in the session
        let nombre = element.parentNode.parentNode.children[1].innerText;
        let active = !isActive;

        fetch('/pagos-administrativos/toggle-pago', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                nombre: nombre,
                active: active
            })
        });
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
    .toggle-eye {
        cursor: pointer;
        font-size: 1.2em;
    }
</style>
