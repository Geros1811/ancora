@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/auth_register.css') }}">
@endsection

@section('content')
    <div class="auth-container">
        <h2>Registrar Equipo</h2>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div>
                <label for="name">Nombre:</label>
                <input type="text" name="name" id="name" required><br>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required><br>
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required><br>
            </div>
            <div>
                <label for="password_confirmation">Confirmar Contraseña:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required><br>
            </div>
            <div>
                <label for="role">Role:</label>
                <select name="role" id="role" required>
                    @guest
                        <option value="arquitecto">Arquitecto</option>
                    @else
                        <option value="cliente">Cliente</option>
                        <option value="arquitecto">Arquitecto</option>
                        <option value="maestro_obra">Residente</option>
                    @endguest
                </select><br>
            </div>

            <button type="submit">Registrar</button>
        </form>

        @if ($errors->any())
            <div>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </div>

@endsection
