<?php
// Démarrage de la session
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "Sakn";
$password = "root";
$dbname = "mon_e-commerce"; // Suppression de l'espace
$port = 3306;

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Fonction pour se connecter
function connecter($email, $password) {
    global $conn;

    // Requête préparée pour éviter les injections SQL
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Fonction pour vérifier les champs
function verifierChamps($email, $password) {
    if (empty($email) || empty($password)) {
        return "Veuillez remplir tous les champs";
    } else {
        return null;
    }
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);

    $erreur = verifierChamps($email, $password);
    if ($erreur !== null) {
        echo $erreur;
    } else {
        // Connexion
        if (connecter($email, $password)) {
            $_SESSION["email"] = $email;
            header("Location: accueil.php");
            exit;
        } else {
            echo "Email ou mot de passe incorrect";
        }
    }
}
?>
