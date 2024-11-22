<?php
// Configuración del Active Directory
$ldap_host = "ldap://192.168.1.50"; // Dirección IP del servidor AD
$ldap_port = 389; // Puerto LDAP predeterminado
$ldap_base_dn = "DC=miempresa,DC=local"; // Base DN del dominio
$ldap_bind_user = "mrojas@miempresa.local"; // Usuario con permisos
$ldap_bind_password = "Tecsup2024"; // Contraseña del usuario

// Conectar al servidor LDAP
$ldap_conn = ldap_connect($ldap_host, $ldap_port);

if (!$ldap_conn) {
    die("No se pudo conectar al servidor LDAP.");
}

// Configurar opciones de LDAP
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

// Autenticar con usuario y contraseña
if (@ldap_bind($ldap_conn, $ldap_bind_user, $ldap_bind_password)) {
    // Filtro para obtener usuarios
    $filter = "(&(objectClass=user))";
    $attributes = ["cn", "samaccountname", "logonCount"]; // Incluye el atributo logonCount

    // Realizar la búsqueda
    $result = ldap_search($ldap_conn, $ldap_base_dn, $filter, $attributes);

    if ($result) {
        $entries = ldap_get_entries($ldap_conn, $result);

        if ($entries["count"] > 0) {
            echo "<h1>Usuarios en Active Directory</h1>";
            echo "<table border='1'>";
            echo "<tr><th>Nombre Completo (CN)</th><th>Nombre de Usuario</th><th>¿Primera vez iniciando sesión?</th></tr>";

            for ($i = 0; $i < $entries["count"]; $i++) {
                $cn = $entries[$i]["cn"][0] ?? "N/A";
                $samaccountname = $entries[$i]["samaccountname"][0] ?? "N/A";
                $logonCount = $entries[$i]["logoncount"][0] ?? 0;

                // Determinar si es la primera vez iniciando sesión
                $isFirstTime = ($logonCount == 0) ? "Sí" : "No";

                echo "<tr><td>$cn</td><td>$samaccountname</td><td>$isFirstTime</td></tr>";
            }

            echo "</table>";
        } else {
            echo "No se encontraron usuarios.";
        }
    } else {
        echo "Error en la búsqueda LDAP.";
    }
} else {
    die("No se pudo autenticar en el Active Directory.");
}

// Cerrar la conexión LDAP
ldap_close($ldap_conn);
?>
