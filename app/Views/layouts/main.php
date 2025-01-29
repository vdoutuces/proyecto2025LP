<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sin Título' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <script src="/js/script.js"></script>

</head>
<body>
<div  class = 'container'>
<header>

        <div class="carousel">
  <div class="slide"><img class='imgban' src='/images/1.jpg'/></div>
  <div class="slide"><img class='imgban' src='/images/2.jpg'/></div>
  <div class="slide"><img class='imgban' src='/images/3.jpg'/></div>
  <button class="prev">Anterior</button>
  <button class="next">Siguiente</button>
</div>

<h1><?= $header ?? 'Mi Sitio Web' ?></h1>
        <nav>


            <ul>
                <li><a href="/">Inicio</a></li>
                <li><a href="/about">Acerca de</a></li>
                <li><a href="/contact">Contacto</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <?= $content ?>
    </main>
    <footer>
        <p>© 2024 Mi Sitio Web</p>
    </footer>
    </div>
</body>
</html>

