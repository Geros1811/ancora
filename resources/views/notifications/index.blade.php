@extends('layouts.app')

@section('content')
    <div class="dashboard-container">
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
                <p>No hay notificaciones.</p>
            @endif
        @endif
    </div>
@endsection
