<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <!-- Mostrar mensajes de éxito o error -->
    <?php
    if (isset($_GET['message'])) {
        echo "<p style='color: " . ($_GET['type'] === 'success' ? 'green' : 'red') . ";'>" . htmlspecialchars($_GET['message']) . "</p>";
    }
    ?>

    <!-- Formulario de inicio de sesión -->
    <form action="process_login.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
