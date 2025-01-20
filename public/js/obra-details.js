function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    section.classList.toggle('hidden-section');
}

function addRow(tbodyId) {
    const tbody = document.getElementById(tbodyId);
    const newRow = tbody.insertRow();
    newRow.innerHTML = `
        <td><input type="text" class="editable"></td>
        <td><input type="date" class="editable"></td>
        <td><input type="number" value="0" class="editable" oninput="updateAcumulado()" onblur="formatCurrency(this)"></td>
        <td><input type="text" value="$0.00" class="editable" disabled></td>
        <td><span onclick="toggleLock(this)" class="lock-icon" style="color: green;">🔓</span></td>
    `;
}

function updateAcumulado() {
    // Lógica para actualizar el acumulado
}

function formatCurrency(input) {
    // Lógica para formatear el valor como moneda
}

function toggleLock(lockIcon) {
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

function guardarCambios() {
    // Lógica para guardar los cambios
}
