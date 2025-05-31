<?php
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['utilisateur']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

require_once 'db.php';

$user_id = $_SESSION['utilisateur']['id'];

// Récupération des données envoyées
$adresse = $input['adresse'] ?? '';
$ville = $input['ville'] ?? '';
$code_postal = $input['code_postal'] ?? '';
$pays = $input['pays'] ?? '';
$telephone = $input['telephone'] ?? '';
$type_carte = $input['type_carte'] ?? '';
$numero_carte = $input['numero_carte'] ?? '';
$nom_carte = $input['nom_carte'] ?? '';
$expiration = $input['expiration'] ?? '';
$code_securite = $input['code_securite'] ?? '';

// Validation des données de paiement (exemple simplifié)
if (empty($numero_carte) || empty($nom_carte) || empty($expiration) || empty($code_securite)) {
    echo json_encode(['success' => false, 'message' => 'Informations de paiement incomplètes']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Enregistrement/MAJ des informations de paiement
    $stmt = $pdo->prepare("SELECT id FROM paiements WHERE user_id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE paiements SET adresse=?, ville=?, code_postal=?, pays=?, telephone=?, type_carte=?, numero_carte=?, nom_carte=?, expiration=?, code_securite=? WHERE user_id=?");
        $stmt->execute([$adresse, $ville, $code_postal, $pays, $telephone, $type_carte, $numero_carte, $nom_carte, $expiration, $code_securite, $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO paiements (user_id, adresse, ville, code_postal, pays, telephone, type_carte, numero_carte, nom_carte, expiration, code_securite)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $adresse, $ville, $code_postal, $pays, $telephone, $type_carte, $numero_carte, $nom_carte, $expiration, $code_securite]);
    }

    // Récupération des articles du panier avec leurs quantités
    $stmt = $pdo->prepare("
        SELECT p.article_id, p.quantity, a.nom, a.prix 
        FROM paniers p 
        JOIN articles a ON p.article_id = a.id 
        WHERE p.acheteur_id = ?
    ");
    $stmt->execute([$user_id]);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($articles)) {
        throw new Exception("Votre panier est vide");
    }

    // Création d'une notification groupée
    $articleList = [];
    $total = 0;
    
    foreach ($articles as $article) {
        $articleList[] = $article['quantity'] . " x " . $article['nom'] . " (" . number_format($article['prix'], 2) . "€)";
        $total += $article['quantity'] * $article['prix'];
        
        // Marquer l'article comme vendu
        $stmt = $pdo->prepare("UPDATE articles SET vendu = TRUE WHERE id = ?");
        $stmt->execute([$article['article_id']]);
    }

    $message = "Vos achats :\n- " . implode("\n- ", $articleList) . 
               "\n\nTotal de : " . number_format($total, 2) . "€";

    $stmtNotif = $pdo->prepare("
        INSERT INTO notifications 
        (utilisateur_id, type_notification, titre, message, date_creation, lue) 
        VALUES (?, 'achat', 'Achat confirmé', ?, NOW(), 0)
    ");
    $stmtNotif->execute([$user_id, $message]);

    // Vider le panier
    $stmt = $pdo->prepare("DELETE FROM paniers WHERE acheteur_id = ?");
    $stmt->execute([$user_id]);

    $pdo->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Paiement enregistré avec succès',
        'redirect' => 'compte.php'  // Redirection vers la page de compte
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur lors du traitement du paiement: ' . $e->getMessage()
    ]);
}
?>