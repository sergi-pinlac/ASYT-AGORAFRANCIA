<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

require_once 'db.php';

$user_id = $_SESSION['utilisateur']['id'];

$stmt = $pdo->prepare("SELECT adresse, ville, code_postal, pays, telephone, type_carte, numero_carte, nom_carte, expiration FROM paiements WHERE user_id = ?");
$stmt->execute([$user_id]);

if ($stmt->rowCount() > 0) {
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'Aucune information de paiement trouvée.']);
}
?>
