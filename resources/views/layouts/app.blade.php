<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi Tienda Admin')</title>
    
    <!-- Bootstrap 5 + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* RESET Y ESTRUCTURA GENERAL */
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ===== SIDEBAR (Menú lateral) ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background-color: #1e1e2f;
            color: #fff;
            padding-top: 20px;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            padding: 0 20px 20px 20px;
            border-bottom: 1px solid #2d2d44;
            text-align: center;
        }
        .sidebar-brand i {
            color: #4fc3f7;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }
        .sidebar-menu li a {
            display: block;
            padding: 12px 25px;
            color: #b3b3cc;
            text-decoration: none;
            transition: 0.2s;
            border-left: 4px solid transparent;
        }
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background-color: #2d2d44;
            color: #fff;
            border-left-color: #4fc3f7;
        }
        .sidebar-menu li a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        /* ===== CONTENIDO PRINCIPAL ===== */
        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
        }

        /* ===== TOP NAVBAR ===== */
        .top-navbar {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-navbar h4 {
            margin: 0;
            font-weight: 600;
            color: #1e1e2f;
        }

        /* ===== RESPONSIVE (Móviles) ===== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .menu-toggle {
                display: inline-block !important;
            }
        }

        .menu-toggle {
            display: none;
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #1e1e2f;
        }

        /* ===== TARJETAS DEL DASHBOARD ===== */
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-3px);
        }
        .card .display-4 {
            font-weight: 700;
        }

        /* Colores de las tarjetas (gradientes) */
        .bg-primary-gradient {
            background: linear-gradient(145deg, #4e73df, #224abe);
        }
        .bg-success-gradient {
            background: linear-gradient(145deg, #1cc88a, #13855c);
        }
        .bg-warning-gradient {
            background: linear-gradient(145deg, #f6c23e, #dda20a);
        }
        .bg-info-gradient {
            background: linear-gradient(145deg, #36b9cc, #258391);
        }

        /* ===== PAGINADO CLÁSICO (Solo estilos, sin flechas ni :contains) ===== */
        .pagination .page-link {
            padding: 4px 10px !important;
            font-size: 0.85rem !important;
            border-radius: 6px !important;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            background: white;
            transition: all 0.15s ease;
        }
        .pagination .page-link:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #0f172a;
        }
        .pagination .page-item.active .page-link {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: white !important;
            font-weight: 600;
        }
        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>
<body>

    <!-- ===== SIDEBAR ===== -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-store-alt"></i> Mi Tienda
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('productos.index') }}" class="{{ request()->routeIs('productos.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Productos
                </a>
            </li>
            <li>
                <a href="{{ route('categorias.index') }}" class="{{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> Categorías
                </a>
            </li>
        </ul>
    </nav>

    <!-- ===== CONTENIDO PRINCIPAL ===== -->
    <div class="main-content">

        <!-- Top Navbar -->
        <div class="top-navbar">
            <div>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h4>@yield('title', 'Panel de Control')</h4>
            </div>
            <div>
                @auth
                    <span class="badge bg-success" style="font-size: 1rem; padding: 8px 15px;">
                        <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                    </span>
                    <a href="{{ route('logout') }}" 
                       class="btn btn-outline-danger btn-sm ms-2"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <span class="badge bg-secondary">
                        <i class="fas fa-user-circle"></i> Invitado
                    </span>
                @endauth
            </div>
        </div>

        <!-- Contenido dinámico de cada página -->
        @yield('content')

    </div>

    <!-- ===== SCRIPTS ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle para el menú en móviles
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Cerrar sidebar al hacer clic fuera en móviles
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('menuToggle');
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
</body>
</html>