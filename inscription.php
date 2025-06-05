<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "Sakn";
$password = "okay123";
$dbname = "mon_e-commerce";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars(trim($_POST["nom"]));
    $prenom = htmlspecialchars(trim($_POST["prenom"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));
    $confirm_password = htmlspecialchars(trim($_POST["confirm_password"]));

    if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "Veuillez remplir tous les champs";
    } elseif ($password !== $confirm_password) {
        echo "Les mots de passe ne correspondent pas";
    } else {
        // Préparation pour éviter les injections SQL
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "L'email est déjà utilisé";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, prenom, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nom, $prenom, $email, $password_hash);

            if ($stmt->execute()) {
                echo "Inscription réussie!";
                header("Location: connexion.php");
                exit;
            } else {
                echo "Erreur lors de l'inscription: " . $stmt->error;
            }
        }

        $stmt->close();
    }
}

$conn->close();
?>
