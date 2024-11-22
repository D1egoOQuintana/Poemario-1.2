<?php
// Configuración del Active Directory
$ldap_host = "ldap://192.168.1.50"; // Dirección IP del servidor AD
$ldap_port = 389; // Puerto LDAP predeterminado
$ldap_base_dn = "DC=miempresa,DC=local"; // Base DN del dominio
$ldap_bind_user = "mrojas@miempresa.local"; // Usuario con permisos
$ldap_bind_password = "Tecsup2024"; // Contraseña del usuario

// Obtener datos del formulario
$username = $_POST['username'];
$password = $_POST['password'];

// Verificar que el nombre de usuario esté presente
if (empty($username)) {
    die("El nombre de usuario es obligatorio.");
}

// Establecer conexión LDAP
$ldap_conn = ldap_connect($ldap_host, $ldap_port);

if (!$ldap_conn) {
    die("No se pudo conectar al servidor LDAP.");
}

// Configurar opciones del protocolo LDAP
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3); // Usar LDAP v3
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);        // Deshabilitar referencias

// Intentar vincularse al AD con un usuario que tenga permisos
if (@ldap_bind($ldap_conn, $ldap_bind_user, $ldap_bind_password)) {
    // Construir el filtro para buscar al usuario
    $filter = "(sAMAccountName=$username)";
    
    // Realizar la búsqueda
    $search = @ldap_search($ldap_conn, $ldap_base_dn, $filter);
    if ($search) {
        $entries = ldap_get_entries($ldap_conn, $search);
        if ($entries["count"] > 0) {
            $user_dn = $entries[0]["dn"]; // Obtener el DN del usuario
            $user_name = $entries[0]["displayname"][0] ?? $username; // Obtener el nombre del usuario o usar el nombre de usuario como predeterminado
            
            if (@ldap_bind($ldap_conn, $user_dn, $password)) {
                // Redirigir al usuario a la página de bienvenida
                session_start();
                $_SESSION['username'] = $user_name; // Guardar el nombre del usuario en la sesión
                header("Location: welcome.php");
                exit();
            } else {
                echo "Error de autenticación: La contraseña no es válida.";
            }
        } else {
            echo "El usuario no existe en el Active Directory.";
        }
    } else {
        echo "Error al realizar la búsqueda LDAP: " . ldap_error($ldap_conn);
    }
} else {
    echo "No se pudo vincular al servidor LDAP con las credenciales proporcionadas.";
}

// Cerrar la conexión
ldap_unbind($ldap_conn);
?>