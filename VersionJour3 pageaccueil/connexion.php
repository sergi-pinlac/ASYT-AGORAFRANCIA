<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=agora_francia;charset=utf8', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && password_verify($password, $utilisateur['mot_de_passe'])) {
        $_SESSION['utilisateur'] = [
            'id' => $utilisateur['id'],
            'nom' => $utilisateur['nom'],
            'prenom' => $utilisateur['prenom'],
            'type' => $utilisateur['type_compte']
        ];
        header("Location: compte.php");
        exit;
    } else {
        echo "❌ Email ou mot de passe incorrect.";
        exit;
    }
}
?>
