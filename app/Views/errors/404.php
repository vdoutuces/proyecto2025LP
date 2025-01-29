<?php  http_response_code(404);?>

<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
</head>
<body>
    <h1>Ha ocurrido un error</h1>
    <p><?= htmlspecialchars($mensaje) ?></p>
</body>
</html>

