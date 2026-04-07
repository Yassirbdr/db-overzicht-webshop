<?php
// ==============================
// pages/products.php – Producten overzicht (verbeterd)
// ==============================

// Includes
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Database connectie
$pdo = getDB();

// Producten ophalen
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Functie voor fallback beschrijvingen
function getDescription($product) {
    if (!empty($product['description'])) {
        return $product['description'];
    }

    $name = strtolower($product['name']);

    switch ($name) {
        case 'headphones':
            return 'Krachtige headphones met diepe bass en helder geluid. Perfect voor muziek en gaming.';
        case 'phone':
            return 'Moderne smartphone met snelle prestaties, scherpe camera en lange batterijduur.';
        case 'laptop':
            return 'Snelle, goede en betrouwbare laptop voor school, werk en dagelijks gebruik.';
        default:
            return 'Geen beschrijving beschikbaar.';
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producten – Webshop</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Eigen CSS -->
    <link rel="stylesheet" href="../styling/style.css">
</head>
<body>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">Alle Producten</h2>
    </div>

    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="product-card h-100 d-flex flex-column">

                        <!-- Product afbeelding -->
                        <img 
                            src="/DB_OVERZICHT/images/<?php echo htmlspecialchars($p['image'] ?? 'placeholder.jpg'); ?>" 
                            alt="<?php echo htmlspecialchars($p['name']); ?>"
                            class="product-image"
                            onerror="this.src='/DB_OVERZICHT/images/placeholder.jpg';"
                        >

                        <div class="card-body d-flex flex-column">
                            
                            <!-- Naam -->
                            <h5 class="mb-1">
                                <?php echo htmlspecialchars($p['name']); ?>
                            </h5>

                            <!-- Beschrijving -->
                            <p class="text-muted small mb-2">
                                <?php echo htmlspecialchars(getDescription($p)); ?>
                            </p>

                            <!-- Prijs -->
                            <p class="price mb-1">
                                €<?php echo number_format($p['price'], 2, ',', '.'); ?>
                            </p>

                            <!-- Voorraad (optioneel later dynamisch maken) -->
                            <p class="small text-muted mb-3">
                                ❌ Niet op voorraad
                            </p>

                            <!-- Knop -->
                            <a href="#" class="btn btn-primary mt-auto">
                                In winkelwagen
                            </a>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-muted text-center">
                    Er zijn nog geen producten in de webshop.
                </p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>