<?php
// ==============================
// pages/login.php
// ==============================

// Include database and auth functions
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// If already logged in, redirect to profile page
if (isLoggedIn()) {
    header("Location: /DB_OVERZICHT/pages/profile.php");
    exit;
}

$error = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if ($email === '' || $password === '') {
        $error = "Vul alle velden in.";
    } else {
        $pdo  = getDB();

        // Prepared statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $customer = $stmt->fetch();

        // Check password
        if ($customer && password_verify($password, $customer['password'])) {
            session_regenerate_id(true);

            // Store user in session
            $_SESSION['customer_id'] = $customer['id'];

            // Flash message
            setFlash('success', "Welkom terug, " . htmlspecialchars($customer['voornaam']) . "!");

            // Redirect to homepage (FIXED PATH)
            header("Location: /DB_OVERZICHT/index.php");
            exit;
        } else {
            $error = "Ongeldig e-mailadres of wachtwoord.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen – Webshop</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styling/style.css">
</head>
<body>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<main class="container py-5" style="max-width:460px;">
    <div class="form-card">
        <h2 class="section-title text-center">Inloggen</h2>

        <!-- Error message -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Login form -->
        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label">E-mailadres</label>
                <input type="email" name="email" class="form-control"
                       value="<?php echo htmlspecialchars($email); ?>" required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label">Wachtwoord</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
                Inloggen
            </button>
        </form>

        <hr class="my-4">

        <p class="text-center mb-0 small">
            Nog geen account?
            <a href="register.php" style="color: var(--clr-accent);">
                Registreren
            </a>
        </p>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>