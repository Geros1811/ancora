<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="notificationModalLabel">Notificaciones</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <h1>Notificaciones</h1>

            @if(Auth::check() && Auth::user()->hasRole('arquitecto'))
                @php
                    $unreadNotifications = Auth::user()->notifications()->whereNull('read_at')->where('obra_id', $obraId ?? null)->get();
                @endphp

                @if($unreadNotifications->count() > 0)
                    <ul>
                        @foreach($unreadNotifications as $notification)
                            <li>{{ $notification->message }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>¡Ya estás al día!
No tienes nuevas notificaciones.</p>
                @endif
            @endif
        </div>
    </div>
</div>
