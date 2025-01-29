<?php 

 function view($view, $data = [], $layout = 'layouts/main') {
    // Extraer las variables para su uso en la vista

	 extract($data);


    // Ruta de la vista
    $viewPath = "../app/Views/{$view}.php";
    if (!file_exists($viewPath)) {
        throw new Exception("La vista {$view} no existe.");
    }

    // Capturar el contenido de la vista principal
    ob_start();
    require $viewPath;
    $content = ob_get_clean();

    // Si hay un layout definido, procesarlo
    if ($layout) {
        $layoutPath = "../app/Views/{$layout}.php";
        if (!file_exists($layoutPath)) {
            throw new Exception("El layout {$layout} no existe.");
        }

        // Pasar el contenido al layout
        ob_start();
        require $layoutPath;
        return ob_get_clean();
    }

    // Si no hay layout, retornar solo la vista
    return $content;
}



function errorView($mensaje) {
    ob_start();
    require '../app/Views/errors/404.php';
    return ob_get_clean();
}


function component($name, $data = []) {
    $componentPath = "../app/Views/components/{$name}.php";

    if (!file_exists($componentPath)) {
        throw new Exception("El componente {$name} no existe.");
    }

    extract($data);
    ob_start();
    require $componentPath;
    return ob_get_clean();
}


    // Responder en JSON
//
function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }


