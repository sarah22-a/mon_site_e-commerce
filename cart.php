<?php
session_start();
require 'db.php';

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Supprimer un produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

// Vider le panier
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
}

// Mettre à jour la quantité
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = (int) $_POST['quantity'];
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity'] = max(1, $new_quantity); // au moins 1
            break;
        }
    }
    unset($item); // éviter les effets secondaires de la référence
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panier - Sakn</title>
    <link rel="stylesheet" href="site.css">
</head>
<body>
    <header>
        <div>
            <span>SA★</span>
            <span>Sakn</span>
        </div>
        <nav>
            <ul>
                <li><a href="/home.php">Accueil</a></li>
                <li><a href="/products.php">Produits</a></li>
                <li><a href="/connexion.php">Connexion</a></li>
                <li><a href="/inscription.php">Inscription</a></li>
                <li><a href="/cart.php">🛒 Panier (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <h2>Mon Panier</h2>

        <?php if (!empty($_SESSION['cart'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Couleur</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <?php $total += $item['price'] * $item['quantity']; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['color']); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                                    <button type="submit" name="update_quantity">Mettre à jour</button>
                                </form>
                            </td>
                            <td><?php echo number_format($item['price'] * $item['quantity'], 2, ',', ' ') . '€'; ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="remove_from_cart">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p><strong>Total : </strong><?php echo number_format($total, 2, ',', ' ') . '€'; ?></p>

            <form method="POST">
                <button type="submit" name="clear_cart">Vider le panier</button>
            </form>
        <?php else: ?>
            <p>Votre panier est vide.</p>
        <?php endif; ?>
    </section>
</body>
</html>
