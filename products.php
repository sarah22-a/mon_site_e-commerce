<?php
session_start();

// Données produits sans images ni liens
$products = [
    ["id" => 1, "name" => "Sweat à capuche zip", "price" => 30.99, "colors" => ["Noir", "Gris", "Bleu"]],
    ["id" => 2, "name" => "Jeans baggy", "price" => 59.99, "colors" => ["Noir", "Bleu clair", "Bleu foncé délavé", "Délavé"]],
    ["id" => 3, "name" => "T-shirt", "price" => 19.99, "colors" => ["Noir", "Navy", "Blanc"]],
    // Ajoute les autres produits comme tu veux...
];

// Gestion ajout au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $color = $_POST['color'] ?? null;
    $size = $_POST['size'] ?? null;

    // Trouver le produit dans la liste
    $product = null;
    foreach ($products as $p) {
        if ($p['id'] === $product_id) {
            $product = $p;
            break;
        }
    }

    if (!$product) {
        $error = "Produit introuvable.";
    } elseif (!$color || !in_array($color, $product['colors'])) {
        $error = "Couleur invalide.";
    } elseif (!$size || !in_array($size, ['S','M','L','XL'])) {
        $error = "Taille invalide.";
    } else {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        // Clé unique pour produit + taille + couleur
        $cartKey = $product_id . '-' . $color . '-' . $size;

        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity']++;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'color' => $color,
                'size' => $size,
                'quantity' => 1,
            ];
        }
        header('Location: cart.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Produits - Sakn</title>
<style>
    body { font-family: Arial, sans-serif; margin: 20px;}
    .product { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
    form { margin-top: 10px; }
</style>
</head>
<body>

<header>
    <h1>Sakn - Boutique</h1>
    <nav>
        <a href="/">Accueil</a> | 
        <a href="/products.php">Produits</a> | 
        <a href="/login.php">Connexion</a> | 
        <a href="/register.php">Inscription</a> | 
        <a href="/cart.php">🛒 Panier (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    </nav>
</header>

<section>
    <h2>Nos Produits</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <?php foreach ($products as $product): ?>
        <div class="product">
            <h3><?= htmlspecialchars($product['name']) ?> — <?= number_format($product['price'], 2) ?> €</h3>

            <form method="POST">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                <label for="color_<?= $product['id'] ?>">Couleur :</label>
                <select name="color" id="color_<?= $product['id'] ?>" required>
                    <option value="" disabled selected>Choisir une couleur</option>
                    <?php foreach ($product['colors'] as $color): ?>
                        <option value="<?= htmlspecialchars($color) ?>"><?= htmlspecialchars($color) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="size_<?= $product['id'] ?>">Taille :</label>
                <select name="size" id="size_<?= $product['id'] ?>" required>
                    <option value="" disabled selected>Choisir une taille</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                </select>

                <button type="submit" name="add_to_cart">Ajouter au panier</button>
            </form>
        </div>
    <?php endforeach; ?>

</section>

</body>
</html>
