<?php
require_once 'db.php'; // Connexion à la BDD

$conditions = [];
$params = [];

// Filtre par type_article[]
if (isset($_GET['type_article']) && is_array($_GET['type_article'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['type_article']), '?'));
    $conditions[] = "type_article IN ($placeholders)";
    $params = array_merge($params, $_GET['type_article']);
}

// Filtre par type_vente[]
if (isset($_GET['type_vente']) && is_array($_GET['type_vente'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['type_vente']), '?'));
    $conditions[] = "type_vente IN ($placeholders)";
    $params = array_merge($params, $_GET['type_vente']);
}

// Filtre par catégorie
if (!empty($_GET['categorie'])) {
    $conditions[] = "type_categorie = ?";
    $params[] = $_GET['categorie'];
}

// Exclure les articles déjà vendus
$conditions[] = "vendu = 0";

// Construction de la requête SQL
$sql = "SELECT * FROM articles";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($articles);
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur serveur']);
}
