<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <title>Sistema de Dotación - IMG</title>
    <style>
        :root {
            --ancho-sidebar: 280px;
            --altura-header: 70px;
            --color-primario: #2563eb;
            --color-secundario: #1e40af;
            --color-acento: #3b82f6;
            --texto-claro: #f8fafc;
            --texto-oscuro: #1e293b;
            --fondo-claro: #f1f5f9;
            --sombra: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --sombra-grande: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--fondo-claro);
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--ancho-sidebar);
            background: linear-gradient(180deg, var(--color-primario) 0%, var(--color-secundario) 100%);
            color: var(--texto-claro);
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.3) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        .sidebar.oculto {
            transform: translateX(-100%);
        }

        .logo-sidebar {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .logo-sidebar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-bottom: 0.5rem;
        }

        .logo-sidebar h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }

        .logo-sidebar small {
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
        }

        .menu-sidebar {
            padding: 1rem 0;
        }

        .item-menu {
            margin: 0.25rem 0;
        }

        .enlace-menu {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--texto-claro);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .enlace-menu:hover {
            background: rgba(255,255,255,0.1);
            color: var(--texto-claro);
            padding-left: 2rem;
        }

        .enlace-menu.activo {
            background: rgba(255,255,255,0.15);
            border-right: 3px solid #60a5fa;
        }

        .enlace-menu i {
            font-size: 1.1rem;
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        .enlace-menu .texto {
            flex: 1;
            font-weight: 500;
        }

        .enlace-menu .flecha {
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .enlace-menu.expandido .flecha {
            transform: rotate(90deg);
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0,0,0,0.1);
        }

        .submenu.abierto {
            max-height: 500px;
        }

        .enlace-submenu {
            display: flex;
            align-items: center;
            padding: 0.6rem 1.5rem 0.6rem 3.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .enlace-submenu:hover {
            background: rgba(255,255,255,0.1);
            color: var(--texto-claro);
            padding-left: 4rem;
        }

        .enlace-submenu.activo {
            background: rgba(255,255,255,0.15);
            color: var(--texto-claro);
        }

        .enlace-submenu i {
            font-size: 0.9rem;
            margin-right: 0.75rem;
            width: 16px;
            text-align: center;
        }

        .header-superior {
            position: fixed;
            top: 0;
            left: var(--ancho-sidebar);
            right: 0;
            height: var(--altura-header);
            background: white;
            box-shadow: var(--sombra);
            z-index: 999;
            transition: left 0.3s ease;
            display: flex;
            align-items: center;
            padding: 0 2rem;
        }

        .header-superior.expandido {
            left: 0;
        }

        .boton-menu {
            background: none;
            border: none;
            font-size: 1.3rem;
            color: var(--texto-oscuro);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: background 0.3s ease;
        }

        .boton-menu:hover {
            background: var(--fondo-claro);
        }

        .info-usuario {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .dropdown-usuario {
            position: relative;
        }

        .boton-usuario {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: none;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .boton-usuario:hover {
            background: var(--fondo-claro);
        }

        .avatar-usuario {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--color-primario);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .menu-usuario {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 0.5rem;
            box-shadow: var(--sombra-grande);
            min-width: 200px;
            padding: 0.5rem 0;
            display: none;
            z-index: 1001;
        }

        .menu-usuario.abierto {
            display: block;
        }

        .item-usuario {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--texto-oscuro);
            text-decoration: none;
        }

        .item-usuario:hover {
            background: var(--fondo-claro);
            color: var(--texto-oscuro);
        }

        .item-usuario.peligro:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .contenido-principal {
            margin-left: var(--ancho-sidebar);
            margin-top: var(--altura-header);
            padding: 2rem;
            min-height: calc(100vh - var(--altura-header));
            transition: margin-left 0.3s ease;
        }

        .contenido-principal.expandido {
            margin-left: 0;
        }

        .overlay {
            display: none;
        }

        .barra-progreso {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: rgba(37, 99, 235, 0.2);
            z-index: 9999;
        }

        .progreso {
            height: 100%;
            background: var(--color-primario);
            width: 0%;
            transition: width 0.3s ease;
        }

        .footer-simple {
            margin-left: var(--ancho-sidebar);
            background: white;
            padding: 1rem 2rem;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
            transition: margin-left 0.3s ease;
        }

        .footer-simple.expandido {
            margin-left: 0;
        }

        @media (max-width: 768px) {
            .contenido-principal {
                padding: 1rem;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .contenido-principal > * {
            animation: fadeIn 0.5s ease;
        }
    </style>
</head>
<body>
    <div class="barra-progreso">
        <div class="progreso" id="barraProgresoElemento"></div>
    </div>

    <div class="overlay" id="overlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="logo-sidebar">
            <img src="<?= asset('./images/cit.png') ?>" alt="Logo">
            <h4>Sistema Dotación</h4>
            <small>Industria Militar Guatemala</small>
        </div>

        <nav class="menu-sidebar">
            <div class="item-menu">
                <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/inicio" class="enlace-menu">
                    <i class="bi bi-house-fill"></i>
                    <span class="texto">Inicio</span>
                </a>
            </div>

            <div class="item-menu">
                <button class="enlace-menu" onclick="alternarSubmenu('menuPersonal')">
                    <i class="bi bi-people"></i>
                    <span class="texto">Personal</span>
                    <i class="bi bi-chevron-right flecha"></i>
                </button>
                <div class="submenu" id="menuPersonal">
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/personalDot" class="enlace-submenu">
                        <i class="bi bi-person-plus"></i>
                        Registro Personal
                    </a>
                </div>
            </div>

            <div class="item-menu">
                <button class="enlace-menu" onclick="alternarSubmenu('menuInventario')">
                    <i class="bi bi-boxes"></i>
                    <span class="texto">Inventario</span>
                    <i class="bi bi-chevron-right flecha"></i>
                </button>
                <div class="submenu" id="menuInventario">
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/prendasDot" class="enlace-submenu">
                        <i class="bi bi-bag"></i>
                        Prendas
                    </a>
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/tallasDot" class="enlace-submenu">
                        <i class="bi bi-file-ruled"></i>
                        Tallas
                    </a>
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/inventarioDot" class="enlace-submenu">
                        <i class="bi bi-clipboard-data"></i>
                        Inventario de la Dotación
                    </a>
                </div>
            </div>

            <div class="item-menu">
                <button class="enlace-menu" onclick="alternarSubmenu('menuDotacion')">
                    <i class="bi bi-box2"></i>
                    <span class="texto">Dotación</span>
                    <i class="bi bi-chevron-right flecha"></i>
                </button>
                <div class="submenu" id="menuDotacion">
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/pedidosDot" class="enlace-submenu">
                        <i class="bi bi-clipboard-check"></i>
                        Pedidos de Dotación
                    </a>
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/entregasDot" class="enlace-submenu">
                        <i class="bi bi-box-seam"></i>
                        Entregas de Dotacion
                    </a>
                </div>
            </div>

            <div class="item-menu">
                <button class="enlace-menu" onclick="alternarSubmenu('menuReportes')">
                    <i class="bi bi-graph-up"></i>
                    <span class="texto">Estadisticas</span>
                    <i class="bi bi-chevron-right flecha"></i>
                </button>
                <div class="submenu" id="menuReportes">
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/estadisticas" class="enlace-submenu">
                        <i class="bi bi-pie-chart"></i>
                        Estadísticas
                    </a>
                </div>
            </div>

            <div class="item-menu">
                <button class="enlace-menu" onclick="alternarSubmenu('menuAdmin')">
                    <i class="bi bi-shield-lock"></i>
                    <span class="texto">Administración</span>
                    <i class="bi bi-chevron-right flecha"></i>
                </button>
                <div class="submenu" id="menuAdmin">
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/usuario" class="enlace-submenu">
                        <i class="bi bi-person-plus"></i>
                        Registrar Usuario
                    </a>
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/aplicaciones" class="enlace-submenu">
                        <i class="bi bi-app"></i>
                        Aplicaciones
                    </a>
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/permisos" class="enlace-submenu">
                        <i class="bi bi-shield-check"></i>
                        Permisos
                    </a>
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/asigPermisos" class="enlace-submenu">
                        <i class="bi bi-person-check"></i>
                        Asignar Permisos
                    </a>
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/historial" class="enlace-submenu">
                        <i class="bi bi-clock-history"></i>
                        Historial de Actividades
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <header class="header-superior" id="headerSuperior">
        <button class="boton-menu" id="botonMenu">
            <i class="bi bi-list"></i>
        </button>

        <div class="info-usuario">
            <div class="dropdown-usuario">
                <button class="boton-usuario" id="botonUsuario">
                    <div class="avatar-usuario">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;">Usuario</div>
                        <div style="font-size: 0.75rem; color: #64748b;">Administrador</div>
                    </div>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="menu-usuario" id="menuUsuario">
                    <div style="height: 1px; background: #e2e8f0; margin: 0.5rem 0;"></div>
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/login" class="item-usuario">
                        <i class="bi bi-box-arrow-left"></i>
                        Iniciar Sesión
                    </a>
                    <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/logout" class="item-usuario peligro">
                        <i class="bi bi-power"></i>
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="contenido-principal" id="contenidoPrincipal">
        <?php echo $contenido; ?>
    </main>

    <footer class="footer-simple" id="footerSimple">
        <div>
            <strong>Sistema de Gestión de Dotación - IMG</strong> - 
            Industria Militar de Guatemala © <?= date('Y') ?>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let sidebarAbierto = true;

        const sidebar = document.getElementById('sidebar');
        const headerSuperior = document.getElementById('headerSuperior');
        const contenidoPrincipal = document.getElementById('contenidoPrincipal');
        const footerSimple = document.getElementById('footerSimple');
        const botonMenu = document.getElementById('botonMenu');
        const overlay = document.getElementById('overlay');
        const botonUsuario = document.getElementById('botonUsuario');
        const menuUsuario = document.getElementById('menuUsuario');
        const barraProgreso = document.getElementById('barraProgresoElemento');

        function alternarSidebar() {
            sidebarAbierto = !sidebarAbierto;
            
            if (sidebarAbierto) {
                sidebar.classList.remove('oculto');
                headerSuperior.classList.remove('expandido');
                contenidoPrincipal.classList.remove('expandido');
                footerSimple.classList.remove('expandido');
            } else {
                sidebar.classList.add('oculto');
                headerSuperior.classList.add('expandido');
                contenidoPrincipal.classList.add('expandido');  
                footerSimple.classList.add('expandido');
            }
        }

        function alternarSubmenu(idSubmenu) {
            const submenu = document.getElementById(idSubmenu);
            const boton = submenu.previousElementSibling;
            
            document.querySelectorAll('.submenu').forEach(sub => {
                if (sub.id !== idSubmenu && sub.classList.contains('abierto')) {
                    sub.classList.remove('abierto');
                    sub.previousElementSibling.classList.remove('expandido');
                }
            });
            
            submenu.classList.toggle('abierto');
            boton.classList.toggle('expandido');
        }

        function alternarMenuUsuario() {
            menuUsuario.classList.toggle('abierto');
        }

        botonMenu.addEventListener('click', alternarSidebar);
        botonUsuario.addEventListener('click', alternarMenuUsuario);

        document.addEventListener('click', function(e) {
            if (!botonUsuario.contains(e.target) && !menuUsuario.contains(e.target)) {
                menuUsuario.classList.remove('abierto');
            }
        });

        const enlaces = document.querySelectorAll('.enlace-menu, .enlace-submenu');
        enlaces.forEach(enlace => {
            enlace.addEventListener('click', function() {
                if (barraProgreso) {
                    barraProgreso.style.width = '100%';
                    setTimeout(() => {
                        barraProgreso.style.width = '0%';
                    }, 500);
                }
            });
        });
    </script>
</body>
</html>