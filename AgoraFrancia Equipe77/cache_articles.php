<?php
$cacheFile = __DIR__ . '/cache_articles.json';
$cacheDuration = 300; // 5 minutes

if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheDuration)) {
    $data = json_decode(file_get_contents($cacheFile), true);
} else {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=agora_francia;charset=utf8', 'root', '');
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }

    $articlesJour = $pdo->query("SELECT id, nom, prix, image_principale, type_vente FROM articles WHERE vendu = 0 ORDER BY RAND() LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    $articlesFlash = $pdo->query("SELECT id, nom, prix, image_principale, type_vente FROM articles WHERE vendu = 0 ORDER BY RAND() LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        'articlesJour' => $articlesJour,
        'articlesFlash' => $articlesFlash
    ];

    file_put_contents($cacheFile, json_encode($data));
}

return $data;
