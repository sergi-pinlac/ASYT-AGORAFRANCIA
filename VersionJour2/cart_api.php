<?php
session_start();
header('Content-Type: application/json');

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

$userId = $_SESSION['user_id'];

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=agora_francia;charset=utf8", "ton_user", "ton_mdp");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion BDD']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'get_cart_items':
        $stmt = $pdo->prepare("SELECT * FROM paniers WHERE acheteur_id = ?");
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($items);
        break;

    case 'get_negotiations':
        $stmt = $pdo->prepare("SELECT * FROM negociations WHERE acheteur_id = ?");
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($items);
        break;

    case 'get_bids':
        $stmt = $pdo->prepare("SELECT * FROM encheres WHERE acheteur_id = ?");
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($items);
        break;

    case 'remove_item':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM paniers WHERE id = ? AND acheteur_id = ?");
            $stmt->execute([$id, $userId]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
        }
        break;

    case 'update_quantity':
        $id = $_GET['id'] ?? null;
        $change = intval($_GET['change'] ?? 0);
        if ($id && $change !== 0) {
            $stmt = $pdo->prepare("UPDATE paniers SET quantity = quantity + ? WHERE id = ? AND acheteur_id = ?");
            $stmt->execute([$change, $id, $userId]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
        }
        break;

    case 'cancel_negotiation':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM negociations WHERE id = ? AND acheteur_id = ?");
            $stmt->execute([$id, $userId]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
        }
        break;

    case 'cancel_bid':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM encheres WHERE id = ? AND acheteur_id = ?");
            $stmt->execute([$id, $userId]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
        }
        break;

    case 'checkout':
        // Ici, tu peux ajouter logique de paiement, etc.
        $stmt = $pdo->prepare("DELETE FROM paniers WHERE acheteur_id = ?");
        $stmt->execute([$userId]);
        echo json_encode(['success' => true, 'message' => 'Achat confirmé']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action inconnue']);
        break;
}
?>
