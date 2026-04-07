<?php
// ==============================
// index.php – Homepagina Webshop (verbeterd)
// ==============================

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

// Database
$pdo = getDB();

// Laatste 6 producten ophalen
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 6");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = getFlash();

// 🔥 Zelfde description functie als products.php
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
            return 'Snelle en betrouwbare laptop voor school, werk en dagelijks gebruik.';
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
    <title>Webshop – Home</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Eigen CSS -->
    <link rel="stylesheet" href="styling/style.css">
</head>
<body>

<?php include __DIR__ . '/partials/navbar.php'; ?>

<main>

    <!-- Flash -->
    <?php if ($flash): ?>
        <div class="container mt-3">
            <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Hero -->
    <div class="hero">
        <div class="container">
            <h1>Welkom bij <span style="color:#60a5fa;">Webshop</span></h1>
            <p class="mb-4">Ontdek de beste producten voor de beste prijs.</p>

            <a href="/DB_OVERZICHT/pages/products.php" class="btn btn-primary px-4 py-2 me-2">
                Bekijk alle producten
            </a>

            <?php if (!isLoggedIn()): ?>
                <a href="/DB_OVERZICHT/pages/register.php" class="btn btn-outline-light px-4 py-2">
                    Account aanmaken
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Producten -->
    <div class="container my-5">
        <h2 class="section-title">Uitgelichte producten</h2>

        <div class="row g-4">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="product-card h-100 d-flex flex-column">

                            <!-- Image -->
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

                                <!-- 🔥 FIXED BESCHRIJVING -->
                                <p class="text-muted small mb-2">
                                    <?php echo htmlspecialchars(getDescription($p)); ?>
                                </p>

                                <!-- Prijs -->
                                <p class="price mb-3">
                                    €<?php echo number_format($p['price'], 2, ',', '.'); ?>
                                </p>

                                <!-- Knop -->
                                <a href="/DB_OVERZICHT/pages/products.php" class="btn btn-primary mt-auto">
                                    Bekijken
                                </a>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-muted text-center">
                        Nog geen producten beschikbaar.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</main>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>