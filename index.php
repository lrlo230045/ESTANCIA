<?php

// Obtiene el controlador desde la URL. Si no existe, usa "login" por defecto.
$controller = $_GET["controller"] ?? "login";

// Obtiene la acción desde la URL. Si no existe, usa "login" por defecto.
$action     = $_GET["action"] ?? "login";

// Construye la ruta del archivo del controlador basado en el nombre recibido.
$controllerFile = "app/controllers/" . ucfirst($controller) . "Controller.php";

// Verifica si el archivo del controlador existe físicamente.
if (file_exists($controllerFile)) {

    // Incluye el archivo del controlador.
    require_once $controllerFile;

    // Construye el nombre de la clase del controlador (ejemplo: LoginController).
    $controllerClass = ucfirst($controller) . "Controller";

    // Instancia el controlador dinámicamente.
    $controlador = new $controllerClass();

    // Verifica si la acción solicitada existe dentro del controlador.
    if (method_exists($controlador, $action)) {

        // Ejecuta la acción (método) del controlador.
        $controlador->$action();

    } else {

        // Si la acción no existe, muestra un mensaje de error.
        echo "<h2>Error: la acción <b>$action</b> no existe en el controlador <b>$controllerClass</b>.</h2>";
    }

} else {

    // Si el controlador no existe, muestra un mensaje de error.
    echo "<h2>Error: el controlador <b>$controller</b> no existe.</h2>";
}

?>
