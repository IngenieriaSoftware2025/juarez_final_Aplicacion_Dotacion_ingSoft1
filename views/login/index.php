<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema JJJC</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            min-height: 100vh;
        }
        
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .form-control {
            border-radius: 15px;
            border: 2px solid #e3e3e3;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
        }
        
        .input-icon input {
            padding-left: 45px;
        }
        
        .logo-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .register-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .custom-title-class {
            color: #2c3e50 !important;
            font-weight: bold !important;
        }

        .custom-text-class {
            color: #34495e !important;
            font-size: 16px !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4 col-xl-3">
            <div class="card login-card shadow-lg">
                <div class="card-body p-5">
                    
                    <!-- Logo y Header -->
                    <div class="text-center mb-4">
                        <div class="logo-container">
                            <i class="bi bi-person-circle text-white fs-1"></i>
                        </div>
                        <h2 class="card-title login-header fw-bold">Iniciar Sesión</h2>
                        <p class="text-muted">Accede a tu cuenta del sistema</p>
                    </div>

                    <!-- Formulario -->
                    <form id="FormLogin" method="POST">
                        
                        <!-- Campo Usuario/Correo -->
                        <div class="mb-4">
                            <label for="usuario_correo" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-2"></i>Correo Electrónico
                            </label>
                            <div class="input-icon">
                                <i class="bi bi-envelope"></i>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="usuario_correo" 
                                    name="usuario_correo"
                                    placeholder="correo@ejemplo.com"
                                    required
                                    autocomplete="email">
                            </div>
                        </div>

                        <!-- Campo Contraseña -->
                        <div class="mb-4">
                            <label for="usuario_contra" class="form-label fw-semibold">
                                <i class="bi bi-lock me-2"></i>Contraseña
                            </label>
                            <div class="input-icon">
                                <i class="bi bi-lock"></i>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="usuario_contra" 
                                    name="usuario_contra"
                                    placeholder="Ingresa tu contraseña"
                                    required
                                    autocomplete="current-password">
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="recordar">
                                <label class="form-check-label text-muted" for="recordar">
                                    Recordar mi sesión
                                </label>
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" id="BtnIniciar" class="btn btn-primary btn-login">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Iniciar Sesión
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <p class="mb-2">
                                <a href="#" class="register-link small">¿Olvidaste tu contraseña?</a>
                            </p>
                            <p class="mb-0">
                                ¿No tienes cuenta? 
                                <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/usuario" class="register-link">Regístrate aquí</a>
                            </p>
                        </div>
                        
                    </form>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-4">
                <p class="text-white small">
                    <i class="bi bi-shield-check me-1"></i>
                    Sistema Seguro JJJC &copy; <?= date('Y') ?>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('build/js/login/index.js') ?>"></script>
</body>
</html>