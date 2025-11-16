<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?? 'AuthBoard' ?></title>
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRUYOFipDcfYvLMm0nJgIcaEaZAz/sUsUfM0uQxRcnXFqPPD" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <header>
        <h1>AuthBoard</h1>
        <?php if (!empty($_SESSION['user'])): ?>
            <nav><a href="/dashboard">Dashboard</a> | <a href="/logout">Logout</a></nav>
        <?php endif; ?>
    </header>

    <main>
        <?php echo $content ; ?>
    </main>

    <footer>
        <small>AuthBoard - teaching project</small>
    </footer>
</div>
</body>
</html>
