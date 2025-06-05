<?php
// Démarrage de la session
session_start();

// Vérification de la session
if (!isset($_SESSION["email"])) {
    header("Location: connexion.php");
    exit;
}

// Informations de l'utilisateur
$email = $_SESSION["email"];

// Connexion à la base de données
$servername = "localhost";
$username = "sakn";
$password = "okay123";
$dbname = "mon_e-commerce";
$port = 3306;

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Préparation et exécution de la requête pour éviter injection SQL
$stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Vérification que l'utilisateur existe
if (!$row) {
    header("Location: connexion.php");
    exit;
}

// Fermeture de la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Accueil</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="container">
        <h1>Bienvenue, <?php echo htmlspecialchars($row["email"]); ?></h1>
        <p>Vous êtes maintenant connecté!</p>
        <p>Votre nom : <?php echo htmlspecialchars($row["nom"]); ?></p>
        <p>Votre prénom : <?php echo htmlspecialchars($row["prenom"]); ?></p>
        <p>Votre email : <?php echo htmlspecialchars($row["email"]); ?></p>
        <a href="deconnexion.php" class="deconnexion-link">Se déconnecter</a>
    </div>
</body>
</html>
