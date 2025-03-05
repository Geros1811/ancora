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
            <h1 class="text-center">Bienvenido, {{ Auth::user()->name }}</h1>
            @if(Auth::user()->role == 'arquitecto')
                @if(Auth::user()->logo && Auth::user()->company_name)
                    <div class="company-info text-center">
                        <img src="{{ asset('storage/' . Auth::user()->logo) }}" alt="Logo" class="company-logo">
                        <h2 class="company-name">{{ Auth::user()->company_name }}</h2>
                    </div>
                @else
                    <div class="text-center">
                        <button id="perfilBtn" class="btn btn-primary">Perfil</button>
                    </div>
                @endif
            @endif
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
    @if(Auth::user()->role == 'arquitecto')
        <a href="{{ route('obra.create') }}">
            <button class="floating-btn">+</button>
        </a>
    @endif

    <!-- Modal -->
    <div id="perfilModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Perfil de Arquitecto</h2>
            <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="company_name">Nombre de la Empresa:</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" value="{{ Auth::user()->company_name }}">
                </div>
                <div class="form-group">
                    <label for="logo">Logo:</label>
                    <input type="file" class="form-control" id="logo" name="logo">
                    @if(Auth::user()->logo)
                        <img src="{{ asset('storage/' . Auth::user()->logo) }}" alt="Logo" class="company-logo">
                    @endif
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const perfilBtn = document.getElementById('perfilBtn');
            const perfilModal = document.getElementById('perfilModal');
            const closeBtn = document.querySelector('.close');

            if (perfilBtn && perfilModal && closeBtn) {
                perfilBtn.addEventListener('click', function() {
                    perfilModal.style.display = 'block';
                });

                closeBtn.addEventListener('click', function() {
                    perfilModal.style.display = 'none';
                });

                window.addEventListener('click', function(event) {
                    if (event.target == perfilModal) {
                        perfilModal.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endsection
