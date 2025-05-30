<?php
session_start();
header('Content-Type: application/json');
require_once('db.php');

$response = ['success' => false];

if (!isset($_SESSION['utilisateur']['id'])) {
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
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$notificationId, $userId]);

    if ($stmt->rowCount() > 0) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Notification introuvable';
    }
} catch (PDOException $e) {
    $response['message'] = 'Erreur : ' . $e->getMessage();
}

echo json_encode($response);
