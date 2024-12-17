<!-- @author Oszaczki Csaba -->

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iskolanévsor</title>
    <link rel="stylesheet" href="projekt.css">
</head>
<body>

<h1>Iskolanévsor</h1>

<!-- Menü -->
<div class="menu">
    <form method="get">
        <button type="submit" name="class" value="all">Minden osztály</button>
        <?php foreach (array_keys($school) as $className): ?>
            <button type="submit" name="class" value="<?= $className ?>"><?= $className ?> osztály</button>
        <?php endforeach; ?>
    </form>
</div>

<div class="menu">
    <form method="post" style="display: inline-block;">
        <button type="submit" name="regenerate">Nevek újragenerálása</button>
    </form>
    <form method="post" style="display: inline-block;">
        <button type="submit" name="show_school_averages">Iskolaátlag megjelenítése</button>
    </form>
    <form method="post" style="display: inline-block;">
        <button type="submit" name="show_best_worst_classes">Legjobb és leggyengébb osztályok megjelenítése</button>
    </form>
    <form method="post" style="display: inline-block;">
        <button type="submit" name="show_ranking">Rangsor megjelenítése</button>
    </form>
</div>

<div class="menu">
    <form method="post" style="display: inline-block;">
        <button type="submit" name="export_csv">Adatok mentése CSV-be</button>
    </form>
    <form method="post" style="display: inline-block;">
        <button type="submit" name="export_school_averages">Iskolaátlag mentése CSV-be</button>
    </form>
    <form method="post" style="display: inline-block;">
        <button type="submit" name="save_best_and_worst_classes">Legjobb és leggyengébb osztályok mentése CSV-be</button>
    </form>
    <form method="post" style="display: inline-block;">
        <button type="submit" name="export_ranking">Rangsorok mentése CSV-be</button>
    </form>
</div>

</body>
</html>