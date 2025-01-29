<?php foreach ($clientes as $cliente): ?>
    <h2><?= $cliente['nombre'] ?> <?= $cliente['apellidos'] ?></h2>
    <p><?= $cliente['correo_electronico'] ?></p>
    <h3>Compras:</h3>
    <ul>
        <?php foreach ($cliente['compras'] as $compra): ?>
            <li><?= $compra['fecha'] ?></li>
        <?php endforeach; ?>
    </ul>
    <a href="/clientes/<?= $cliente['id'] ?>">Ver detalles</a>
<?php endforeach; ?>
