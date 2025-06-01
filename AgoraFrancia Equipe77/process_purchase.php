<?php
session_start(); // Doit être la première ligne
header('Content-Type: application/json');
require_once('db.php');

$response = ['success' => false, 'message' => ''];

// Vérification de la connexion adaptée à votre système
if (!isset($_SESSION['utilisateur']) || !isset($_SESSION['utilisateur']['id'])) {
    $response['message'] = 'Veuillez vous connecter pour effectuer cette action';
    echo json_encode($response);
    exit;
}

$userId = $_SESSION['utilisateur']['id']; // Adapté à votre structure de session

try {
    if (!isset($_POST['article_id'])) {
        throw new Exception('ID article manquant');
    }

    $articleId = intval($_POST['article_id']);

    // Récupérer l'article
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$articleId]);
    $article = $stmt->fetch();

    if (!$article) {
        throw new Exception('Article non trouvé');
    }

    // Traiter selon le type d'achat (identique à votre version originale)
    switch($article['type_vente']) {
        case 'achat_immediat':
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
                if ($quantity < 1) throw new Exception('Quantité invalide');

                // Vérifier le stock
                if ($article['quantity'] < $quantity) {
                    throw new Exception("Stock insuffisant. Il ne reste que {$article['quantity']} exemplaire(s) de cet article.");
                }

                // Mettre à jour le stock
                $newStock = $article['quantity'] - $quantity;
                $stmt = $pdo->prepare("UPDATE articles SET quantity = ? WHERE id = ?");
                $stmt->execute([$newStock, $articleId]);

                // Ajouter au panier
                $stmt = $pdo->prepare("INSERT INTO paniers (acheteur_id, article_id, statut, date_ajout) VALUES (?, ?, 'en_attente', NOW())");
                $stmt->execute([$userId, $articleId]);
                $stmt = $pdo->prepare("INSERT INTO notifications (utilisateur_id, type_notification, titre, message, article_id) VALUES (?, 'achat', 'Ajouté au panier', ?, ?)");
$stmt->execute([$userId, "Votre achat de {$quantity} x {$article['nom']} a été ajouté au panier.", $articleId]);
// Notifier le vendeur
$stmt = $pdo->prepare("INSERT INTO notifications (utilisateur_id, type_notification, titre, message, article_id) VALUES (?, 'achat', 'Article vendu', ?, ?)");
$stmt->execute([$article['vendeur_id'], "Votre article '{$article['nom']}' a été acheté par un utilisateur.", $articleId]);

            
            $response['message'] = "{$quantity} x {$article['nom']} ajouté au panier";   
            break;

        case 'negociation':
            $offerPrice = floatval($_POST['offer_price']);
                if ($offerPrice <= 0) throw new Exception('Prix invalide');

                // Déterminer le prochain tour
                $stmt = $pdo->prepare("SELECT MAX(tour) as max_tour FROM negociations WHERE article_id = ? AND acheteur_id = ?");
                $stmt->execute([$articleId, $userId]);
                $maxTour = $stmt->fetch()['max_tour'];
                $tour = ($maxTour ?? 0) + 1;

                // Enregistrer l'offre
                $stmt = $pdo->prepare("INSERT INTO negociations (article_id, acheteur_id, offre, tour) VALUES (?, ?, ?, ?)");
                $stmt->execute([$articleId, $userId, $offerPrice, $tour]);
$stmt = $pdo->prepare("INSERT INTO notifications (utilisateur_id, type_notification, titre, message, article_id) VALUES (?, 'negociation', 'Offre envoyée', ?, ?)");
$stmt->execute([$userId, "Votre offre de {$offerPrice}€ pour {$article['nom']} a été envoyée au vendeur.", $articleId]);
                $response['message'] = "Votre offre de {$offerPrice}€ (tour {$tour}) pour {$article['nom']} a été envoyée au vendeur";
                // Notifier le vendeur d'une nouvelle offre
$stmt = $pdo->prepare("INSERT INTO notifications (utilisateur_id, type_notification, titre, message, article_id) VALUES (?, 'negociation', 'Nouvelle offre reçue', ?, ?)");
$stmt->execute([$article['vendeur_id'], "Vous avez reçu une offre de {$offerPrice}€ pour '{$article['nom']}'.", $articleId]);

            break;

       case 'enchere':
            $bidPrice = floatval($_POST['bid_price']);
            if ($bidPrice <= 0) throw new Exception('Enchère invalide');

            // Vérifier si c'est la meilleure offre
            $stmt = $pdo->prepare("SELECT MAX(montant_max) as max_prix FROM encheres WHERE article_id = ?");
            $stmt->execute([$articleId]);
            $maxBid = $stmt->fetch()['max_prix'];

            if ($bidPrice <= ($maxBid ?? 0)) {
                throw new Exception("Votre enchère doit être supérieure à {$maxBid}€");
            }

            // Enregistrer l'enchère
            $stmt = $pdo->prepare("INSERT INTO encheres (acheteur_id, article_id, montant_max) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $articleId, $bidPrice]);
$stmt = $pdo->prepare("INSERT INTO notifications (utilisateur_id, type_notification, titre, message, article_id) VALUES (?, 'enchere', 'Enchère placée', ?, ?)");
$stmt->execute([$userId, "Votre enchère de {$bidPrice}€ pour {$article['nom']} a été enregistrée.", $articleId]);
            $response['message'] = "Votre enchère de {$bidPrice}€ pour {$article['nom']} a été enregistrée";
            // Notifier le vendeur d'une nouvelle enchère
$stmt = $pdo->prepare("INSERT INTO notifications (utilisateur_id, type_notification, titre, message, article_id) VALUES (?, 'enchere', 'Nouvelle enchère placée', ?, ?)");
$stmt->execute([$article['vendeur_id'], "Une nouvelle enchère de {$bidPrice}€ a été placée sur votre article '{$article['nom']}'.", $articleId]);

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