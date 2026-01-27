<?php
$choix = "";

if (!empty($_POST['mot'])) {
    $mots = array_filter($_POST['mot']);

    if (!empty($mots)) {
        $choix = $mots[array_rand($mots)];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choix aléatoire</title>
</head>
<body>

<h2>Formulaire de choix</h2>

<form method="post">
    <?php for ($i = 1; $i <= 10; $i++): ?>
        <label>Choix <?= $i ?> :</label>
        <input type="text" name="mot[]">
        <br><br>
    <?php endfor; ?>

    <input type="submit" value="Choisir au hasard">
</form>

<?php if ($choix): ?>
    <h3>Résultat</h3>
    <p>Choix sélectionné : <strong><?= htmlspecialchars($choix) ?></strong></p>
<?php endif; ?>

</body>
</html>
