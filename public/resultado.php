<?php
// ... (conexión a la base de datos) ...
echo "post ----";

var_dump($_POST);

echo "END_ POST ....";


// Endpoint para recibir datos del formulario (ejemplo: /api/guardar_datos)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/resultado.php') {
  // Obtener datos del formulario (JSON)
  $datos_json = file_get_contents('php://input');
  $datos = json_decode($datos_json, true); // true para array asociativo


  var_dump($datos_json);

  var_dump($datos);

  // Verificar si se recibieron datos
  if ($datos !== null) {
    // Procesar los datos (ejemplo: guardar en la base de datos)
//    foreach ($datos as $fila => $valor) {

	    $nombre = $datos['nombre'];
      $email = $datos['email'];

      // ... (insertar datos en la base de datos) ...
      $sql = "INSERT INTO usuarios (nombre, email) VALUES ('$nombre', '$email')";
      # $conexion->query($sql);
      echo "$sql";;
  //  }

    // Enviar respuesta exitosa (código 200)
    http_response_code(200);
    echo json_encode(array('mensaje' => 'Datos guardados correctamente'));
  } else {
    // Enviar respuesta de error (código 400)
    http_response_code(400);
    echo json_encode(array('mensaje' => 'Error al recibir los datos'));
  }
} else {
  // Endpoint no válido
  http_response_code(404);
  echo json_encode(array('mensaje' => 'Endpoint no encontrado'));
}
