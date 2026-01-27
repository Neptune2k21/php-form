<?php
$choix = "";

$host = getenv('MYSQL_HOST') ?: 'db';
$user = getenv('MYSQL_USER') ?: 'root';
$pass = getenv('MYSQL_PASSWORD') ?: 'rootpassword';
$dbname = getenv('MYSQL_DATABASE') ?: 'form_alea';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}



$numCats = 1000;
$catEmojis = ['🐱', '😸', '🐈', '😺', '🐈‍⬛', '😻', '😹', '😽'];

if (!empty($_POST['mot'])) {
    $mots = array_filter($_POST['mot']);

    if (!empty($mots)) {
        $choix = $mots[array_rand($mots)];

        // Insérer dans la BDD
        $stmt = $pdo->prepare("INSERT INTO historique (choix) VALUES (?)");
        $stmt->execute([$choix]);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choix aléatoire</title>
    <style>
        body {
            font-family: 'Comic Sans MS', cursive, sans-serif;
            background: linear-gradient(45deg, #ff0000, #ff8000, #ffff00, #80ff00, #00ff00, #00ff80, #00ffff, #0080ff, #0000ff, #8000ff, #ff00ff, #ff0080);
            background-size: 400% 400%;
            animation: disco 2s ease-in-out infinite;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            margin: 0;
            padding: 20px;
        }

        @keyframes disco {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        h2 {
            font-size: 3em;
            text-align: center;
            animation: rainbow 1s infinite;
        }

        @keyframes rainbow {
            0% { color: red; }
            16% { color: orange; }
            33% { color: yellow; }
            50% { color: green; }
            66% { color: blue; }
            83% { color: indigo; }
            100% { color: violet; }
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(255,255,255,0.5);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 2px solid;
            border-image: linear-gradient(45deg, red, orange, yellow, green, blue, indigo, violet) 1;
            background: rgba(255,255,255,0.8);
            color: black;
            border-radius: 5px;
        }

        input[type="submit"] {
            background: linear-gradient(45deg, #ff0000, #ffff00);
            color: black;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        input[type="submit"]:hover {
            transform: scale(1.1);
            animation: pulse 0.5s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 10px rgba(255,255,255,0.5); }
            50% { box-shadow: 0 0 20px rgba(255,255,255,1); }
            100% { box-shadow: 0 0 10px rgba(255,255,255,0.5); }
        }

        h3 {
            text-align: center;
            font-size: 2em;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.5; }
        }

        p {
            font-size: 1.5em;
            text-align: center;
        }

        strong {
            font-size: 2em;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        #cat-curtain {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: none;
            z-index: 1000;
            overflow: hidden;
        }

        .falling-cat {
            position: absolute;
            font-size: 3em;
            animation: fall 5s linear infinite;
        }

        @keyframes fall {
            0% { top: -50px; transform: rotate(0deg); }
            100% { top: 100vh; transform: rotate(360deg); }
        }

        #cat-emoji {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 4em;
            cursor: pointer;
            z-index: 999;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        #france{
            position: fixed;
            right: 10px;
            top: 10px;
            height: 800px;
            width: 300px;
        }
    </style>
</head>
<body>

<h2>Formulaire de choix</h2>

<p>Liste des développeurs :</p>
<ul>
    <li>Mamadou CISSE</li>
    <li>Enzo VANDEPOELE</li>
    <li>Romain DURAND</li>
</ul>

<form method="post">
    <?php for ($i = 1; $i <= 10; $i++): ?>
        <label>Choix <?= $i ?> :</label>
        <input type="text" name="mot[]">
        <br><br>
    <?php endfor; ?>

    <input type="submit" id="submit-btn" value="Choisir au hasard" style="position: absolute;">
</form>

<?php if ($choix): ?>
    <h3>Résultat</h3>
    <p>Choix sélectionné : <strong><?= htmlspecialchars($choix) ?></strong></p>
<?php endif; ?>

<div id="cat-curtain">
    <?php for ($i = 0; $i < $numCats; $i++): ?>
        <div class="falling-cat" style="left: <?= rand(0, 90) ?>%; animation-delay: <?= rand(0, 500) / 100 ?>s;"><?= $catEmojis[array_rand($catEmojis)] ?></div>
    <?php endfor; ?>
</div>

<span id="cat-emoji">🐱</span>

<img src="france.jpg" id="france">

<script>
    document.getElementById('cat-emoji').addEventListener('click', function() {
        const curtain = document.getElementById('cat-curtain');
        curtain.style.display = 'block';
        setTimeout(() => {
            curtain.style.display = 'none';
        }, 10000);
    });

    let hoverCount = 0;
    const submitBtn = document.getElementById('submit-btn');

    submitBtn.addEventListener('mouseover', function() {
        if (hoverCount < 5) {
            hoverCount++;
            const maxX = window.innerWidth - submitBtn.offsetWidth;
            const maxY = window.innerHeight - submitBtn.offsetHeight;
            const randomX = Math.random() * maxX;
            const randomY = Math.random() * maxY;
            submitBtn.style.left = randomX + 'px';
            submitBtn.style.top = randomY + 'px';
        }
    });
</script>

</body>
</html>