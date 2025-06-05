<?php
session_start();

// Traitement : mise à jour, suppression, vidage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'], $_POST['key'], $_POST['quantity'])) {
        $key = $_POST['key'];
        $quantity = max(1, (int)$_POST['quantity']);
        $_SESSION['cart'][$key]['quantity'] = $quantity;
        header('Location: cart.php');
        exit;
    } elseif (isset($_POST['clear_cart'])) {
        unset($_SESSION['cart']);
        header('Location: cart.php');
        exit;
    }
}

if (isset($_GET['remove'])) {
    $key = $_GET['remove'];
    unset($_SESSION['cart'][$key]);
    header('Location: cart.php');
    exit;
}

// Préparation des données
$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="../assets/site.css">
</head>
<body>
    <header>
        <div><span>SA🌟</span></div>
        <nav>
            <ul>
                <li><a href="index.php">🏡 Accueil</a></li>
                <li><a href="product.php">🏷️ Produits</a></li>
                <li><a href="connexion.php">🔐 Connexion</a></li>
                <li><a href="inscription.php">📝 Inscription</a></li>
                <li><a href="cart.php">🛒 Panier (<?php echo count($cart); ?>)</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Mon Panier</h2>

        <?php if (!empty($cart)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Couleur</th>
                        <th>Taille</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $key => $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['color']); ?></td>
                            <td><?php echo htmlspecialchars($item['size']); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="key" value="<?php echo $key; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
                                    <button type="submit" name="update_quantity">Mettre à jour</button>
                                </form>
                            </td>
                            <td><?php echo number_format($subtotal, 2, ',', ' ') . ' €'; ?></td>
                            <td>
                                <a href="cart.php?remove=<?php echo urlencode($key); ?>">❌ Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p><strong>Total :</strong> <?php echo number_format($total, 2, ',', ' ') . ' €'; ?></p>

            <form method="POST">
                <button type="submit" name="clear_cart">🧹 Vider le panier</button>
            </form>
        <?php else: ?>
            <p>Votre panier est vide.</p>
        <?php endif; ?>
    </main>
</body>
</html>
