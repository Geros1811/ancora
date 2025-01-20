<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My App')</title>
    @yield('head')  <!-- Aquí se inyectan los estilos CSS o scripts -->
</head>
<body>
    <div class="container">
        @yield('content')  <!-- Aquí se renderiza el contenido de cada vista -->
    </div>
</body>
</html>
