<?php
header('Content-Type: application/json');
require_once('db.php');

$response = ['success' => false, 'message' => ''];

try {
    if (!isset($_POST['article_id'])) {
        throw new Exception('ID article manquant');
    }

    $articleId = intval($_POST['article_id']);
    $userId = 1; // À remplacer par l'ID de l'utilisateur connecté

    // Récupérer l'article
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$articleId]);
    $article = $stmt->fetch();

    if (!$article) {
        throw new Exception('Article non trouvé');
    }

    // Traiter selon le type d'achat
    switch($article['type_vente']) {
        case 'achat_immediat':
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
            if ($quantity < 1) throw new Exception('Quantité invalide');
            
            // Ici vous devriez vérifier le stock, etc.
            $total = $article['prix'] * $quantity;
            
            // Ajouter au panier
            $stmt = $pdo->prepare("INSERT INTO panier (user_id, article_id, quantity, prix_unitaire) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $articleId, $quantity, $article['prix']]);
            
            $response['message'] = "{$quantity} x {$article['nom']} ajouté au panier pour {$total}€";
            break;

        case 'negociation':
            $offerPrice = floatval($_POST['offer_price']);
            if ($offerPrice <= 0) throw new Exception('Prix invalide');
            
            // Enregistrer l'offre
            $stmt = $pdo->prepare("INSERT INTO negociations (user_id, article_id, prix_propose) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $articleId, $offerPrice]);
            
            $response['message'] = "Votre offre de {$offerPrice}€ pour {$article['nom']} a été envoyée au vendeur";
            break;

        case 'enchere':
            $bidPrice = floatval($_POST['bid_price']);
            if ($bidPrice <= 0) throw new Exception('Enchère invalide');
            
            // Vérifier si c'est la meilleure offre
            $stmt = $pdo->prepare("SELECT MAX(prix) as max_prix FROM encheres WHERE article_id = ?");
            $stmt->execute([$articleId]);
            $maxBid = $stmt->fetch()['max_prix'];
            
            if ($bidPrice <= ($maxBid ?? 0)) {
                throw new Exception("Votre enchère doit être supérieure à {$maxBid}€");
            }
            
            // Enregistrer l'enchère
            $stmt = $pdo->prepare("INSERT INTO encheres (user_id, article_id, prix) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $articleId, $bidPrice]);
            
            $response['message'] = "Votre enchère de {$bidPrice}€ pour {$article['nom']} a été enregistrée";
            break;

        default:
            throw new Exception('Type de vente non supporté');
    }

    $response['success'] = true;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>