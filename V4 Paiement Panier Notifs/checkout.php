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

try {
    // Vérification si utilisateur a déjà payé
    $stmt = $pdo->prepare("SELECT id FROM paiements WHERE user_id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() > 0) {
        // Update
        $stmt = $pdo->prepare("UPDATE paiements SET adresse=?, ville=?, code_postal=?, pays=?, telephone=?, type_carte=?, numero_carte=?, nom_carte=?, expiration=?, code_securite=? WHERE user_id=?");
        $stmt->execute([$adresse, $ville, $code_postal, $pays, $telephone, $type_carte, $numero_carte, $nom_carte, $expiration, $code_securite, $user_id]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO paiements (user_id, adresse, ville, code_postal, pays, telephone, type_carte, numero_carte, nom_carte, expiration, code_securite)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $adresse, $ville, $code_postal, $pays, $telephone, $type_carte, $numero_carte, $nom_carte, $expiration, $code_securite]);
    }

    // Vider le panier après paiement
    $stmt = $pdo->prepare("DELETE FROM paniers WHERE acheteur_id = ?");
    $stmt->execute([$user_id]);

    echo json_encode([
        'success' => true, 
        'message' => 'Paiement enregistré avec succès',
        'redirect' => 'panier.html'  // Ajout de l'URL de redirection
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur lors du traitement du paiement: ' . $e->getMessage()
    ]);
}
?>