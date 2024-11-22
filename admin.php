<?php
session_start();

// Verificación más estricta del rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['mensaje'] = "Acceso denegado. Se requieren privilegios de administrador.";
    header("Location: home.php");
    exit();
}

// Configuración del Active Directory
$ldap_host = "ldap://192.168.1.50";
$ldap_port = 389;
$ldap_base_dn = "DC=miempresa,DC=local";
$ldap_bind_user = "mrojas@miempresa.local";
$ldap_bind_password = "Tecsup2024";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="admin.css"> <!-- Asegúrate de que la ruta sea correcta -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Asegúrate de que la ruta sea correcta -->
    <style>
        body { padding-top: 60px; }
        .admin-container { padding: 20px; }
        .admin-card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand text-center mx-auto" href="home.php" style="font-family: 'Pacifico', cursive; font-weight: 400; font-style: normal; font-size:60px;">Textealo - Panel Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Poemas</a>
                    </li>
                    <?php if (isset($_SESSION['nombre_usuario'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Perfil (<?php echo $_SESSION['nombre_usuario']; ?>)</a>
                        </li>
                        <?php if ($_SESSION['rol'] == 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin.php">Administración</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Login
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="registro.php">Inicio de Sesion</a></li>
                                <li><a class="dropdown-item" href="registro.php">Registro</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                <form class="d-flex mt-3" role="search">
                    <input class="form-control me-2" type="search" placeholder="Buscame" aria-label="Search">
                    <button class="btn btn-success" type="submit">Busqueda</button>
                </form>
            </div>
        </div>
    </div>
</nav>
    <div class="container admin-container">
        <div class="row">
            <div class="col-md-4">
                <div class="card admin-card">
                    <div class="card-body">
                        <h5 class="card-title">Gestión de Usuarios</h5>
                        <p class="card-text">Administra los usuarios del sistema</p>
                        <a href="listar.php" class="btn btn-primary">Ver Usuarios</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card admin-card">
                    <div class="card-body">
                        <h5 class="card-title">Gestión de Poemas</h5>
                        <p class="card-text">Administra los poemas publicados</p>
                        <a href="#" class="btn btn-primary">Gestionar Poemas</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card admin-card">
                    <div class="card-body">
                        <h5 class="card-title">Estadísticas</h5>
                        <p class="card-text">Ver estadísticas del sistema</p>
                        <a href="#" class="btn btn-primary">Ver Estadísticas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Derechos de autor Diego Quintana. Todos los derechos reservados.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
function mostrarMensaje(mensaje) {
    var alerta = document.createElement("div");
    alerta.innerText = mensaje;
    alerta.style.position = "fixed";
    alerta.style.bottom = "10px";
    alerta.style.right = "10px";
    alerta.style.backgroundColor = "rgba(0, 0, 0, 0.7)";
    alerta.style.color = "#fff";
    alerta.style.padding = "10px 20px";
    alerta.style.borderRadius = "5px";
    alerta.style.zIndex = "1000";
    document.body.appendChild(alerta);
    
    setTimeout(function() {
        alerta.remove();
    }, 4000); // Ocultar el mensaje después de 4 segundos
}
function mostrarPerfil() {
    document.getElementById('login-menu').classList.add('d-none');
    document.getElementById('profile-menu').classList.remove('d-none');
}
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<!-- Bootstrap CSS -->

<!-- Bootstrap JS (para hacer funcionar el menú dropdown) -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
</body>
</body>
</html>