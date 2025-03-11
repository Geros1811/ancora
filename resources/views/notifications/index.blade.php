<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="notificationModalLabel">📢 Notificaciones</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            @if(Auth::check() && Auth::user()->hasRole('arquitecto'))
                @php
                    $obraId = $obra->id;
                    $unreadNotifications = Auth::user()->notifications()->whereNull('read_at')->where('obra_id', $obraId)->get();
                @endphp

                @if($unreadNotifications->count() > 0)
                <ul class="list-group">
                    @foreach($unreadNotifications as $notification)
                        <li class="list-group-item d-flex justify-content-between align-items-center notification-item unread" 
                            data-id="{{ $notification->id }}">
                            <span>
                                <strong>{{ $notification->message }}</strong>
                                <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                            </span>
                            <span class="notification-status">
                                <span class="badge badge-danger">🔴</span> <!-- Asegurar que el punto rojo esté aquí -->
                            </span>
                        </li>
                    @endforeach
                </ul>
                @else
                    <div class="text-center text-muted py-3">
                        <p>✅ ¡Ya estás al día! No tienes nuevas notificaciones.</p>
                    </div>
                @endif
            @else
                <div class="text-center text-muted py-3">
                    <p>No tienes permisos para ver notificaciones.</p>
                </div>
            @endif
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".notification-item").on("click", function() {
            let notificationId = $(this).data("id");
            console.log("Notification ID:", notificationId); // Add this line
            let notificationItem = $(this);

            $.ajax({
                url: "/marcar-notificacion-leida",
                type: "POST",
                data: {
                    id: notificationId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // Elimina el punto rojo
                        notificationItem.find(".notification-status").html("");
                        // Cambia el fondo a indicar que está leído
                        notificationItem.removeClass("unread").addClass("read");
                    }
                },
                error: function() {
                    alert("Hubo un error al marcar la notificación como leída.");
                }
            });
        });
    });
</script>


<style>
    .unread {
    background-color: #f8d7da; /* Rojo claro */
    cursor: pointer;
}

.read {
    background-color: #d4edda; /* Verde claro */
}
</style>
