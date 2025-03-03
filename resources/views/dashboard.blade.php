@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
    <div class="dashboard-container">
        <!-- Botón de logout en la parte superior izquierda -->
        <div class="logout-container">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>

        <!-- Sección de bienvenida -->
        <div class="welcome-container">
            <h1>Welcome, {{ Auth::user()->name }}</h1>
        </div>

        <!-- Sección de obras -->
        <div class="obras-container">
            <h2>Obras Creadas</h2>
            <div class="obras-list">
                @if($obras->count() === 0)
                    <p>No hay obras creadas aún.</p>
                @else
                <ul>
                    @foreach ($obras as $obra)
                        <li>
                            <a href="{{ route('obra.show', $obra->id) }}" class="obra-link">
                                <div>
                                    <strong>{{ $obra->nombre }}</strong>
                                </div>
                                <div>
                                    <span>${{ number_format($obra->presupuesto, 2) }}</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>

    <!-- Botón flotante que redirige a la creación de obra -->
    @if(Auth::user()->role != 'cliente')
        <a href="{{ route('obra.create') }}">
            <button class="floating-btn">+</button>
        </a>
    @endif
@endsection
