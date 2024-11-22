<?php
include 'ldap_config.php';

function ldap_authenticate($username, $password) {
    global $ldap_host, $ldap_port, $ldap_dn, $ldap_user, $ldap_password;

    // Conectar al servidor LDAP
    $ldap_conn = ldap_connect($ldap_host, $ldap_port);
    if (!$ldap_conn) {
        error_log("Error de conexión LDAP: No se pudo conectar al servidor");
        return false;
    }

    // Configurar opciones LDAP
    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
    
    try {
        // Intentar bind con las credenciales de administrador
        if (!@ldap_bind($ldap_conn, $ldap_user, $ldap_password)) {
            error_log("Error LDAP: No se pudo realizar el bind inicial - " . ldap_error($ldap_conn));
            return false;
        }

        // Construir filtro de búsqueda
        $filter = "(sAMAccountName=$username)";
        
        // Realizar búsqueda
        $search = @ldap_search($ldap_conn, $ldap_dn, $filter);
        if (!$search) {
            error_log("Error LDAP en búsqueda: " . ldap_error($ldap_conn));
            return false;
        }

        $entries = ldap_get_entries($ldap_conn, $search);
        if ($entries['count'] == 0) {
            error_log("Usuario no encontrado: $username");
            return false;
        }

        // Intentar autenticar con las credenciales del usuario
        $user_dn = $entries[0]['dn'];
        if (@ldap_bind($ldap_conn, $user_dn, $password)) {
            return true;
        } else {
            error_log("Contraseña incorrecta para usuario: $username");
            return false;
        }
    } catch (Exception $e) {
        error_log("Error LDAP: " . $e->getMessage());
        return false;
    } finally {
        ldap_close($ldap_conn);
    }
}
?>