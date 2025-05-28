<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'agora_francia';
$user = 'root';
$pass = ''; // ou ton mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage des données
    $vendeur_id      = $_POST['vendeur_id'] ?? null;
    $nom             = $_POST['nom'] ?? '';
    $description     = $_POST['description'] ?? '';
    $prix            = $_POST['prix'] ?? 0;
    $type_vente      = $_POST['type_vente'] ?? '';
    $type_article    = $_POST['type_article'] ?? '';
    $image_principale = $_POST['image_principale'] ?? '';
    $video_url       = $_POST['video_url'] ?? '';

    // Vérifie que l'ID vendeur existe
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE id = :id AND type = 'vendeur'");
    $stmt->execute([':id' => $vendeur_id]);
    if ($stmt->rowCount() === 0) {
        die("❌ Erreur : le vendeur avec l'ID $vendeur_id n'existe pas ou n'est pas un vendeur.");
    }

    // Insertion dans la table articles
    $sql = "INSERT INTO articles (vendeur_id, nom, description, prix, type_vente, type_article, image_principale, video_url)
            VALUES (:vendeur_id, :nom, :description, :prix, :type_vente, :type_article, :image_principale, :video_url)";

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':vendeur_id' => $vendeur_id,
            ':nom' => $nom,
            ':description' => $description,
            ':prix' => $prix,
            ':type_vente' => $type_vente,
            ':type_article' => $type_article,
            ':image_principale' => $image_principale,
            ':video_url' => $video_url
        ]);

        echo "<h2>✅ Article ajouté avec succès !</h2>";
        echo "<a href='accueil.html'>Retour à l'accueil</a>";
    } catch (PDOException $e) {
        echo "<h2>❌ Erreur lors de l'insertion :</h2>";
        echo "<p>" . $e->getMessage() . "</p>";
    }
} else {
    echo "<h2>❌ Requête invalide.</h2>";
}
?>
