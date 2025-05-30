<?php
$host = 'localhost';
$db = 'agora_francia';
$user = 'root';
$pass = ''; // si tu as défini un mot de passe MySQL, ajoute-le ici

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Erreur de connexion : " . $e->getMessage());
}
?>
