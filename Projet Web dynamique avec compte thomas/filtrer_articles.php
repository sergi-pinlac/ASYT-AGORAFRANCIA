<?php
require_once 'db.php';

$type_article = $_GET['type_article'] ?? [];
$type_vente = $_GET['type_vente'] ?? [];
$categorie = $_GET['categorie'] ?? '';

$params = [];
$where = [];

if (!empty($type_article)) {
    $in = str_repeat('?,', count($type_article) - 1) . '?';
    $where[] = "type_article IN ($in)";
    $params = array_merge($params, $type_article);
}

if (!empty($type_vente)) {
    $in = str_repeat('?,', count($type_vente) - 1) . '?';
    $where[] = "type_vente IN ($in)";
    $params = array_merge($params, $type_vente);
}

if (!empty($categorie)) {
    $where[] = "categorie = ?";
    $params[] = $categorie;
}

$sql = "SELECT * FROM articles";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($articles);
