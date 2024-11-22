<?php
session_start();

// Configuración del Active Directory
$ldap_host = "ldap://192.168.1.50";
$ldap_port = 389;
$ldap_base_dn = "DC=miempresa,DC=local";
$ldap_bind_user = "mrojas@miempresa.local";
$ldap_bind_password = "Tecsup2024";

$mensaje = '';

// Procesar inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['usuario'], $_POST['contraseña'])) {
    $username = $_POST['usuario'];
    $password = $_POST['contraseña'];

    if (empty($username)) {
        $mensaje = "El nombre de usuario es obligatorio.";
    } else {
        $ldap_conn = ldap_connect($ldap_host, $ldap_port);

        if (!$ldap_conn) {
            $mensaje = "No se pudo conectar al servidor LDAP.";
        } else {
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

            if (@ldap_bind($ldap_conn, $ldap_bind_user, $ldap_bind_password)) {
                $filter = "(sAMAccountName=$username)";
                $search = @ldap_search($ldap_conn, $ldap_base_dn, $filter);
                
                if ($search) {
                    $entries = ldap_get_entries($ldap_conn, $search);
                    if ($entries["count"] > 0) {
                        $user_dn = $entries[0]["dn"];
                        $user_name = $entries[0]["displayname"][0] ?? $username;
                        
                        if (@ldap_bind($ldap_conn, $user_dn, $password)) {
                            // Verificar si el usuario es miembro del grupo de administradores
                            $admin_group_dn = "CN=Administradores,CN=Builtin," . $ldap_base_dn;
                            $admin_filter = "(&(objectClass=user)(sAMAccountName=$username)(memberOf:1.2.840.113556.1.4.1941:=$admin_group_dn))";
                            $admin_search = @ldap_search($ldap_conn, $ldap_base_dn, $admin_filter);
                            
                            $_SESSION['username'] = $user_name;
                            $_SESSION['nombre_usuario'] = $user_name;
                            
                            if ($admin_search && ldap_count_entries($ldap_conn, $admin_search) > 0) {
                                $_SESSION['rol'] = 'admin';
                            } else {
                                $_SESSION['rol'] = 'usuario';
                            }
                            
                            // Para depuración
                            error_log("Usuario: " . $user_name . " - Rol: " . $_SESSION['rol']);
                            
                            header("Location: home.php");
                            exit();
                        } else {
                            $mensaje = "Error de autenticación: La contraseña no es válida.";
                        }
                    } else {
                        $mensaje = "El usuario no existe en el Active Directory.";
                    }
                } else {
                    $mensaje = "Error al realizar la búsqueda LDAP: " . ldap_error($ldap_conn);
                }
            } else {
                $mensaje = "No se pudo vincular al servidor LDAP con las credenciales proporcionadas.";
            }
            ldap_unbind($ldap_conn);
        }
    }
}

// Modificar el manejo de mensajes para usar las nuevas clases CSS
if (!empty($mensaje)) {
    $messageClass = strpos($mensaje, "Error") !== false ? "error-message" : "success-message";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="registro.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <title>Bienvenido a mi Formulario</title>
</head>
<body>

    <div class="container-form sign-up">    
        <div class="welcome-back">
            <div class="message">
                <h2>Bienvenido a Textealo</h2>
                <p>Si ya tienes una cuenta por favor inicia sesion aqui</p>
                <button class="sign-up-btn">Iniciar Sesion</button>
            </div>
        </div>
        <form class="formulario" action="registro.php" method="POST">
            <h2 class="create-account">Crear una cuenta</h2>
            <div class="iconos">
                <div class="border-icon">
                    <i class='bx bxl-instagram'></i>
                </div>
                <div class="border-icon">
                    <i class='bx bxl-linkedin'></i>
                </div>
                <div class="border-icon">
                    <i class='bx bxl-facebook-circle'></i>
                </div>
            </div>
            <p class="cuenta-gratis">Crear una cuenta gratis</p>
            <input type="text" name="nombre" placeholder="Nombre">
            <input type="email" name="email" placeholder="Email">
            <input type="password" name="contraseña" placeholder="Contraseña">
            <select name="rol" required>
                    <option value="usuario">Usuario</option>
                    <option value="admin">Admin</option>
            </select>
            <input type="submit" value="Registrarse">
           
            <!-- Mostrar mensaje de error o éxito -->
            <?php if (!empty($mensaje)) : ?>
                <p class="<?php echo $messageClass; ?>"><?php echo $mensaje; ?></p>
            <?php endif; ?>
        </form>
<!-- Mostrar el mensaje de error si existe -->
    <?php if(!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    </div>
    <div class="container-form sign-in">
    <form class="formulario" action="registro.php" method="POST">
        <h2 class="create-account">Iniciar Sesion</h2>
        <div class="iconos">
            <div class="border-icon">
                <i class='bx bxl-instagram'></i>
            </div>
            <div class="border-icon">
                <i class='bx bxl-linkedin'></i>
            </div>
            <div class="border-icon">
                <i class='bx bxl-facebook-circle'></i>
            </div>
        </div>
        <p class="cuenta-gratis">¿Aun no tienes una cuenta?</p>
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="contraseña" placeholder="Contraseña" required>
        <input type="submit" value="Iniciar Sesion">
        <?php if (!empty($mensaje)) : ?>
                <p class="<?php echo $messageClass; ?>"><?php echo $mensaje; ?></p>
            <?php endif; ?>
    </form>
    <div class="welcome-back">
        <div class="message">
            <h2>Bienvenido de nuevo</h2>
            <p>Si aun no tienes una cuenta por favor registrese aqui</p>
            <button class="sign-in-btn">Registrarse</button>
        </div>
    </div>
</div>
    <script src="script.js"></script>
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
</script>

</body>

</html>