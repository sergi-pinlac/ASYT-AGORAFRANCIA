<?php
header('Content-Type: application/json');
require_once('db.php');

if (!isset($_GET['id'])) {
  echo json_encode(['error' => 'ID manquant']);
  exit;
}

try {
  $pdo = new PDO("mysql:host=localhost;dbname=agora_francia", "root", "");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
  $stmt->execute([$_GET['id']]);
  $article = $stmt->fetch(PDO::FETCH_ASSOC);

  echo json_encode($article ?: ['error' => 'Article non trouvé']);
} catch (Exception $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
?>
