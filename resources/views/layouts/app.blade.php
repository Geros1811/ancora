<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Ancora')</title>
  <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" 
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link href="{{ asset('css/gastos_rapidos.css') }}" rel="stylesheet">
  @yield('head')
  <!-- Estilos adicionales para el botón hamburguesa y el menú -->
  <style>
    /* Reset del body */
    body {
      margin: 0;
    }
    /* Botón hamburguesa */
    .navbar-toggler {
      position: absolute;
      top: 20px;
      left: 20px;
      border: none;
      background: transparent;
      cursor: pointer;
      z-index: 1000;
      width: 40px;
      height: 35px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      font-size: 30px; /* Increase font size */
    }
    .navbar-toggler .line {
      display: block;
      width: 100%;
      height: 3px;
      background: #000;
      transition: transform 0.3s ease, opacity 0.3s ease;
    }
    /* Transición para formar una "X" al activar */
    .navbar-toggler.active .line1 {
      transform: translateY(11px) rotate(45deg);
    }
    .navbar-toggler.active .line2 {
      opacity: 0;
    }
    .navbar-toggler.active .line3 {
      transform: translateY(-11px) rotate(-45deg);
    }
    /* Regla para ocultar el botón de menú principal cuando está activo */
    button[data-target="#mainMenu"].active {
      display: none;
    }
    /* Menú lateral utilizando Flexbox */
    #mainMenu {
      position: fixed;
      top: 0;
      left: -250px; /* Comienza fuera de la pantalla */
      width: 250px;
      height: 100%;
      background-color: #f8f9fa;
      padding-top: 50px; /* Espacio para el toggler */
      transition: left 0.3s ease;
      z-index: 999;
      display: flex;
      flex-direction: column;
    }
    #mainMenu.show {
      left: 0; /* Desliza el menú a la vista */
    }
    /* Lista de navegación: ocupará el espacio disponible y tendrá scroll si es necesario */
    #mainMenu .navbar-nav {
      flex: 1;
      overflow-y: auto;
      margin: 0;
      padding: 0;
      list-style: none;
    }
    #mainMenu .nav-link {
      color: #007bff;
      font-weight: bold;
      padding: 10px 20px;
      display: block;
      text-decoration: none;
    }
    #mainMenu .nav-link:hover {
      color: #0056b3;
      background-color: rgba(0, 0, 0, 0.05);
    }
    /* Espacio a la derecha del icono */
    #mainMenu .nav-link i {
      margin-right: 10px;
    }
    /* Enlace de registrar, ubicado al final de la lista (dentro del menú) */
    /* (Se integra dentro del <ul> en este ejemplo) */
    /* Estilo para el overlay */
    .menu-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 998;
      display: none;
    }
    .menu-overlay.show {
      display: block;
    }
  </style>
</head>
<body>
  <div id="app">
    <div class="container">
      @yield('content')
    </div>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
      <div class="container">
        <!-- Botón para colapsar el contenido principal (si aplica) -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" 
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        @if(!request()->routeIs('login'))
          <!-- Botón de hamburguesa para el menú principal -->
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainMenu" 
                  aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle main menu">
            &#9776;
          </button>
        @endif
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- ...existing navbar content... -->
        </div>
        @if(!request()->routeIs('login'))
          <div class="collapse" id="mainMenu">
            <ul class="navbar-nav ml-auto">
              @if(Auth::check() && Auth::user()->role == 'cliente')
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                  </a>
                </li>
                 {{-- Mostrar el enlace "Home" solo si NO estamos en la ruta "home" --}}
                 @if (!request()->routeIs('home'))
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('obra.show', ['id' => $obra->id]) : '#' }}">
                      <i class="fas fa-home"></i>Home
                    </a>
                  </li>
                @endif
                <li class="nav-item">
                  <a class="nav-link" href="{{ isset($obra) ? route('cliente_fotos.index', ['obraId' => $obra->id]) : route('cliente_fotos.index', ['obraId' => 1]) }}">
                    <i class="fas fa-image"></i>Fotos cliente
                  </a>
                </li>
              @else
                @if(request()->routeIs('register') || request()->routeIs('obra.create'))
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                      <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                  </li>
                @else
                  {{-- Mostrar el enlace "Home" solo si NO estamos en la ruta "home" --}}
                  @if (!request()->routeIs('home'))
                    <li class="nav-item">
                      <a class="nav-link" href="{{ isset($obra) ? route('obra.show', ['id' => $obra->id]) : '#' }}">
                        <i class="fas fa-home"></i>Home
                      </a>
                    </li>
                  @endif
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('papeleria.index', ['obraId' => $obra->id]) : route('papeleria.index', ['obraId' => 1]) }}">
                      <i class="fas fa-file"></i>Papelería
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('gasolina.index', ['obraId' => $obra->id]) : route('gasolina.index', ['obraId' => 1]) }}">
                      <i class="fas fa-gas-pump"></i>Gasolina
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('rentas.index', ['obraId' => $obra->id]) : route('rentas.index', ['obraId' => 1]) }}">
                      <i class="fas fa-hand-holding-usd"></i>Rentas
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('utilidades.index', ['obraId' => $obra->id]) : route('utilidades.index', ['obraId' => 1]) }}">
                      <i class="fas fa-chart-line"></i>Utilidades
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('acarreos.index', ['obraId' => $obra->id]) : route('acarreos.index', ['obraId' => 1]) }}">
                      <i class="fas fa-truck"></i>Acarreos
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('comidas.index', ['obraId' => $obra->id]) : route('comidas.index', ['obraId' => 1]) }}">
                      <i class="fas fa-utensils"></i>Comidas
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('tramites.index', ['obraId' => $obra->id]) : route('tramites.index', ['obraId' => 1]) }}">
                      <i class="fas fa-folder"></i>Trámites
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('cimbras.index', ['obraId' => $obra->id]) : route('cimbras.index', ['obraId' => 1]) }}">
                      <i class="fas fa-building"></i>Cimbras
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('maquinariaMayor.index', ['obraId' => $obra->id]) : route('maquinariaMayor.index', ['obraId' => 1]) }}">
                      <i class="fas fa-cogs"></i>Maquinaria Mayor
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('rentaMaquinaria.index', ['obraId' => $obra->id]) : route('rentaMaquinaria.index', ['obraId' => 1]) }}">
                      <i class="fas fa-truck-loading"></i>Renta Maquinaria
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('maquinariaMenor.index', ['obraId' => $obra->id]) : route('maquinariaMenor.index', ['obraId' => 1]) }}">
                      <i class="fas fa-tools"></i>Maquinaria Menor
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('limpieza.index', ['obraId' => $obra->id]) : route('limpieza.index', ['obraId' => 1]) }}">
                      <i class="fas fa-broom"></i>Limpieza
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('herramientaMenor.index', ['obraId' => $obra->id]) : route('herramientaMenor.index', ['obraId' => 1]) }}">
                      <i class="fas fa-hammer"></i>Herramienta Menor
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('equipoSeguridad.index', ['obraId' => $obra->id]) : route('equipoSeguridad.index', ['obraId' => 1]) }}">
                      <i class="fas fa-hard-hat"></i>Equipo Seguridad
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('manoObra.index', ['obraId' => $obra->id]) : route('manoObra.index', ['obraId' => 1]) }}">
                      <i class="fas fa-users"></i>Mano Obra
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('materiales.index', ['obraId' => $obra->id]) : route('materiales.index', ['obraId' => 1]) }}">
                      <i class="fas fa-cubes"></i>Materiales
                    </a>
                  </li>
                  @if(Auth::check() && (Auth::user()->role != 'maestro_obra' && Auth::user()->role != 'residente'))
                    <li class="nav-item">
                      <a class="nav-link" href="{{ isset($obra) ? route('destajos.index', ['obraId' => $obra->id]) : route('destajos.index', ['obraId' => 1]) }}">
                        <i class="fas fa-tasks"></i>Destajos
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ isset($obra) ? route('ingresos.index', ['obraId' => $obra->id]) : route('ingresos.index', ['obraId' => 1]) }}">
                        <i class="fas fa-money-bill-wave"></i>Ingresos
                      </a>
                    </li>
                  @endif
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                      <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ isset($obra) ? route('cliente_fotos.index', ['obraId' => $obra->id]) : route('cliente_fotos.index', ['obraId' => 1]) }}">
                      <i class="fas fa-image"></i>Fotos cliente
                    </a>
                  </li>
                  @if(Auth::user()->role != 'maestro_obra' && Auth::user()->role != 'residente')
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('obra.create') }}">
                        <i class="fas fa-plus"></i>Crear Obra
                      </a>
                    </li>
                  @endif
                  @if(Auth::check() && Auth::user()->hasRole('arquitecto'))
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('register') }}">
                        <i class="fas fa-user-plus"></i>Registrar
                      </a>
                    </li>
                  @endif
                @endif
              @endif
            </ul>
          </div>
        @endif
      </div>
    </nav>
  </div>
  <!-- JavaScript para controlar la animación y el funcionamiento del menú -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const toggler = document.querySelectorAll('.navbar-toggler');
      const menu = document.getElementById('mainMenu');
      const overlay = document.createElement('div');
      overlay.className = 'menu-overlay';
      document.body.appendChild(overlay);
      function toggleMenu() {
        menu.classList.toggle('show');
        overlay.classList.toggle('show');
        toggler.forEach(btn => btn.classList.toggle('active'));
        // Prevenir scroll en el body mientras el menú esté abierto
        document.body.style.overflow = menu.classList.contains('show') ? 'hidden' : '';
      }
      toggler.forEach(btn => btn.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMenu();
      }));
      overlay.addEventListener('click', toggleMenu);
      // Cierra el menú si se hace click fuera de él
      document.addEventListener('click', function(event) {
        if (!menu.contains(event.target) && menu.classList.contains('show')) {
          toggleMenu();
        }
      });
      // Previene que el scroll en el menú afecte al resto de la página
      menu.addEventListener('wheel', function(event) {
        event.stopPropagation();
      });
    });
  </script>
</body>
</html>
