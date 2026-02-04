<?php

use PHPUnit\Framework\TestCase;

// On définit PHPUNIT_RUNNING pour désactiver les affichages dans index.php
if (!defined("PHPUNIT_RUNNING")) {
  define("PHPUNIT_RUNNING", true);
}

class IndexTest extends TestCase
{
  private $pdo;

  protected function setUp(): void
  {
    // FORCE 127.0.0.1 au lieu de 'db' pour les tests locaux hors Docker
    $host = "127.0.0.1";
    $user = getenv("MYSQL_USER") ?: "root";
    $pass = getenv("MYSQL_PASSWORD") ?: "rootpassword";
    $dbname = getenv("MYSQL_DATABASE") ?: "form_alea";

    // On injecte ces variables dans l'environnement pour index.php
    putenv("MYSQL_HOST=$host");

    try {
      $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      $this->markTestSkipped("Serveur MySQL local introuvable sur 127.0.0.1. Vérifiez qu'il est lancé.");
    }
  }

  public function testPageLoadsSuccessfully(): void
  {
    $_POST = [];

    // On initialise une variable pour éviter l'erreur de buffer non fermé
    $output = "";
    ob_start();

    try {
      // Utilisation de require pour être sûr du chemin
      require __DIR__ . "/../index.php";
      $output = ob_get_contents();
    } catch (\Throwable $t) {
      // Si index.php lance une exception ou un die()
      $output = "Erreur capturée : " . $t->getMessage();
    } finally {
      // On ferme TOUJOURS le buffer pour éviter le message "Risky"
      ob_end_clean();
    }

    $this->assertStringContainsString("Formulaire de choix", $output);
  }

  public function testDatabaseInsertionWorks(): void
  {
    // Valeur très courte pour éviter l'erreur SQL "Data too long"
    $testChoice = "T" . rand(100, 999);

    $stmt = $this->pdo->prepare("INSERT INTO historique (choix) VALUES (?)");
    $result = $stmt->execute([$testChoice]);
    $this->assertTrue($result);

    $stmt = $this->pdo->prepare("SELECT choix FROM historique WHERE choix = ?");
    $stmt->execute([$testChoice]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $this->assertEquals($testChoice, $row["choix"]);

    // Nettoyage après test
    $this->pdo->prepare("DELETE FROM historique WHERE choix = ?")->execute([$testChoice]);
  }
}
