<?php
header('Content-Type: application/json');
require_once('db.php'); // si tu as un fichier db.php, sinon ajoute la connexion ici

try {
  $pdo = new PDO("mysql:host=localhost;dbname=agora_francia", "root", ""); // adapte les identifiants
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $pdo->query("SELECT * FROM articles");
  $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($articles);
} catch (Exception $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
?>