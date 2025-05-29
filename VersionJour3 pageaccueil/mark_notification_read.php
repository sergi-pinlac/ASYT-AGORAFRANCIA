<?php
session_start();
header('Content-Type: application/json');
require_once('db.php');

$response = ['success' => false];

if (!isset($_SESSION['utilisateur']) || !isset($_SESSION['utilisateur']['id'])) {
    $response['message'] = 'Non autorisé';
    echo json_encode($response);
    exit;
}

if (!isset($_POST['id'])) {
    $response['message'] = 'ID notification manquant';
    echo json_encode($response);
    exit;
}

$notificationId = intval($_POST['id']);
$userId = $_SESSION['utilisateur']['id'];

try {
    // Vérifier que la notification appartient bien à l'utilisateur
    $stmt = $pdo->prepare("UPDATE notifications SET lue = TRUE WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$notificationId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Notification non trouvée ou déjà lue';
    }
} catch (PDOException $e) {
    $response['message'] = 'Erreur de base de données';
}

echo json_encode($response);
?>