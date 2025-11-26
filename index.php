
<?php
$controller = $_GET["controller"] ?? "login";
$action     = $_GET["action"] ?? "login";

$controllerFile = "app/controllers/" . ucfirst($controller) . "Controller.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = ucfirst($controller) . "Controller";
    $controlador = new $controllerClass();

    if (method_exists($controlador, $action)) {
        $controlador->$action();
    } else {
        echo "<h2>Error: la acción <b>$action</b> no existe en el controlador <b>$controllerClass</b>.</h2>";
    }
} else {
    echo "<h2>Error: el controlador <b>$controller</b> no existe.</h2>";
}
?>
