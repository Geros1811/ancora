@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <div class="register-container">
        <h2>Register</h2>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div>
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required><br>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required><br>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required><br>
            </div>
            <div>
                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required><br>
            </div>
            <div>
                <label for="role">Role:</label>
                <select name="role" id="role" required>
                    <option value="arquitecto">Arquitecto</option>
                    <option value="maestro_obra">Maestro de Obra</option>
                    <option value="cliente">Cliente</option>
                </select><br>
            </div>
            <button type="submit">Register</button>
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
