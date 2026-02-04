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

// Créer la table migrations si elle n'existe pas
$stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS migrations (id VARCHAR(30) PRIMARY KEY)");
$stmt->execute();

// Lire le fichier migrations.json
$migrationsFile = __DIR__ . '/migrations.json';
if (!file_exists($migrationsFile)) {
    die("Fichier migrations.json introuvable");
}

$migrationsJson = file_get_contents($migrationsFile);
$migrations = json_decode($migrationsJson, true);

if ($migrations === null) {
    die("Erreur lors de la lecture du fichier migrations.json");
}

// Parcourir toutes les migrations
foreach ($migrations as $migration) {
    $migrationId = $migration['id'];

    // Vérifier si la migration a déjà été exécutée
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM migrations WHERE id = ?");
    $stmt->execute([$migrationId]);
    $exists = $stmt->fetchColumn() > 0;

    if ($exists) {
        echo "Migration $migrationId déjà exécutée, ignorée.\n";
        continue;
    }

    // Exécuter la migration
    echo "Exécution de la migration $migrationId...\n";
    try {
        $pdo->beginTransaction();

        // Exécuter chaque script de la migration
        foreach ($migration['scripts'] as $script) {
            $pdo->exec($script);
        }

        // Enregistrer la migration comme exécutée
        $stmt = $pdo->prepare("INSERT INTO migrations (id) VALUES (?)");
        $stmt->execute([$migrationId]);

        $pdo->commit();
        echo "Migration $migrationId exécutée avec succès.\n";
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Erreur lors de l'exécution de la migration $migrationId : " . $e->getMessage());
    }
}

echo "Toutes les migrations ont été traitées.\n";

