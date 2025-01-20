@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <div class="login-container">
        <h2>Login</h2>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required><br>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required><br>
            </div>
            <button type="submit">Login</button>
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
