<?php


	$datos = array(
  'nombre' => 'Dato automático',
  'email' => 'email@ejemplo.com',
  'otro_campo' => 'Valor'
);

// URL de la API
$url = 'http://localhost/resultado.php';
$opciones = array(
  'http' => array(
    'method' => 'POST',
    'header' => 'Content-type: application/json',
    'content' => json_encode($datos) // Codificamos los datos a JSON
  )
);

// Envío de la solicitud
$contexto = stream_context_create($opciones);
$resultado = file_get_contents($url, false, $contexto);

// Manejo de la respuesta
if ($resultado !== false) {
  echo "Formulario enviado exitosamente.\n";
  // Puedes decodificar la respuesta JSON si la API devuelve datos
  $respuesta = json_decode($resultado, true); // El segundo parámetro 'true' indica que queremos un array asociativo
  // ... (procesar la respuesta de la API) ...


  var_dump($resultado);

} else {
  echo "Error al enviar el formulario.\n";
}

