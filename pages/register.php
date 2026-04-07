<?php
// ==============================
// pages/register.php  –  Account registreren
// ==============================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Als gebruiker al ingelogd is, stuur door naar profiel
if (isLoggedIn()) {
    header("Location: /DB_OVERZICHT/pages/profile.php");
    exit;
}

$errors = [];
$values = ['voornaam' => '', 'achternaam' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voornaam   = trim($_POST['voornaam']   ?? '');
    $achternaam = trim($_POST['achternaam'] ?? '');
    $email      = trim($_POST['email']      ?? '');
    $password   = $_POST['password']        ?? '';
    $password2  = $_POST['password2']       ?? '';

    $values = compact('voornaam', 'achternaam', 'email');

    // Validatie
    if ($voornaam === '')    $errors[] = "Voornaam is verplicht.";
    if ($achternaam === '')  $errors[] = "Achternaam is verplicht.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Ongeldig e-mailadres.";
    if (strlen($password) < 8) $errors[] = "Wachtwoord moet minimaal 8 tekens zijn.";
    if ($password !== $password2) $errors[] = "Wachtwoorden komen niet overeen.";

    if (empty($errors)) {
        $pdo  = getDB();

        // Check of e-mail al bestaat
        $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = "Dit e-mailadres is al in gebruik.";
        } else {
            // Wachtwoord hashen en gebruiker aanmaken
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins  = $pdo->prepare(
                "INSERT INTO customers (voornaam, achternaam, email, password) 
                 VALUES (?, ?, ?, ?)"
            );
            $ins->execute([$voornaam, $achternaam, $email, $hash]);

            // Automatisch inloggen na registratie
            $_SESSION['customer_id'] = $pdo->lastInsertId();

            setFlash('success', "Welkom, $voornaam! Je account is succesvol aangemaakt.");
            header("Location: /DB_OVERZICHT/pages/profile.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren – Webshop</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Eigen styling -->
    <link rel="stylesheet" href="../styling/style.css">
</head>
<body>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<main class="container py-5" style="max-width:520px;">
    <div class="form-card">
        <h2 class="section-title text-center">Account aanmaken</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $e): ?>
                        <li><?php echo htmlspecialchars($e); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label">Voornaam *</label>
                    <input type="text" name="voornaam" class="form-control"
                           value="<?php echo htmlspecialchars($values['voornaam']); ?>" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Achternaam *</label>
                    <input type="text" name="achternaam" class="form-control"
                           value="<?php echo htmlspecialchars($values['achternaam']); ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label">E-mailadres *</label>
                    <input type="email" name="email" class="form-control"
                           value="<?php echo htmlspecialchars($values['email']); ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Wachtwoord <small class="text-muted">(min. 8 tekens)</small></label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Herhaal wachtwoord</label>
                    <input type="password" name="password2" class="form-control" required>
                </div>

                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        Registreren
                    </button>
                </div>
            </div>
        </form>

        <hr class="my-4">

        <p class="text-center mb-0 small">
            Al een account? 
            <a href="login.php" style="color: var(--clr-accent);">Inloggen</a>
        </p>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>