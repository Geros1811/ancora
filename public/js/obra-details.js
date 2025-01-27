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
