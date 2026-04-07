<?php
// ==============================
// pages/profile.php  –  NAW gegevens beheren
// ==============================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireLogin();   // Zorgt dat je ingelogd moet zijn

$pdo    = getDB();
$id     = currentCustomerId();

// Haal klantdata op
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$customer = $stmt->fetch();

$errors  = [];
$success = false;
$tab     = $_GET['tab'] ?? 'naw';   // 'naw' of 'password'

// ── NAW gegevens opslaan ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_naw'])) {
    $fields = ['voornaam','achternaam','adres','postcode','stad','land','telefoon'];
    $data   = [];

    foreach ($fields as $f) {
        $data[$f] = trim($_POST[$f] ?? '');
    }

    if ($data['voornaam'] === '')   $errors[] = "Voornaam is verplicht.";
    if ($data['achternaam'] === '') $errors[] = "Achternaam is verplicht.";

    // Eenvoudige postcode check
    if ($data['postcode'] !== '' && !preg_match('/^\d{4}\s?[A-Za-z]{2}$/', $data['postcode'])) {
        $errors[] = "Postcode heeft geen geldig formaat (bijv. 1234 AB).";
    }

    if (empty($errors)) {
        $upd = $pdo->prepare("
            UPDATE customers SET
                voornaam=:voornaam, achternaam=:achternaam,
                adres=:adres, postcode=:postcode,
                stad=:stad, land=:land, telefoon=:telefoon
            WHERE id=:id
        ");
        $upd->execute(array_merge($data, ['id' => $id]));

        // Data verversen
        $stmt->execute([$id]);
        $customer = $stmt->fetch();

        setFlash('success', "NAW-gegevens succesvol opgeslagen.");
        header("Location: /DB_OVERZICHT/pages/profile.php?tab=naw");
        exit;
    }
    $tab = 'naw';
}

// ── Wachtwoord wijzigen ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_password'])) {
    $huidig  = $_POST['huidig_wachtwoord'] ?? '';
    $nieuw   = $_POST['nieuw_wachtwoord']  ?? '';
    $confirm = $_POST['bevestig_wachtwoord'] ?? '';

    if (!password_verify($huidig, $customer['password'])) {
        $errors[] = "Huidig wachtwoord is onjuist.";
    }
    if (strlen($nieuw) < 8) {
        $errors[] = "Nieuw wachtwoord moet minimaal 8 tekens zijn.";
    }
    if ($nieuw !== $confirm) {
        $errors[] = "Nieuwe wachtwoorden komen niet overeen.";
    }

    if (empty($errors)) {
        $hash = password_hash($nieuw, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE customers SET password = ? WHERE id = ?")
            ->execute([$hash, $id]);

        setFlash('success', "Wachtwoord succesvol gewijzigd.");
        header("Location: /DB_OVERZICHT/pages/profile.php?tab=password");
        exit;
    }
    $tab = 'password';
}

// Initialen voor avatar
$initials = strtoupper(
    substr($customer['voornaam'] ?? 'K', 0, 1) .
    substr($customer['achternaam'] ?? '', 0, 1)
);

$flash = getFlash();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Account – Webshop</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Eigen styling -->
    <link rel="stylesheet" href="../styling/style.css">
</head>
<body>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<main class="container py-5">

    <!-- Flash bericht -->
    <?php if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="profile-sidebar text-center">
                <div class="profile-avatar"><?php echo htmlspecialchars($initials); ?></div>
                <h6 class="fw-bold mb-0">
                    <?php echo htmlspecialchars(trim(($customer['voornaam'] ?? '') . ' ' . ($customer['achternaam'] ?? ''))); ?>
                </h6>
                <p class="small text-muted mb-3"><?php echo htmlspecialchars($customer['email'] ?? ''); ?></p>

                <div class="d-grid gap-2">
                    <a href="?tab=naw" 
                       class="btn btn-sm <?php echo $tab === 'naw' ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                        📋 NAW Gegevens
                    </a>
                    <a href="?tab=password" 
                       class="btn btn-sm <?php echo $tab === 'password' ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                        🔒 Wachtwoord wijzigen
                    </a>
                    <a href="/DB_OVERZICHT/pages/logout.php" 
                       class="btn btn-sm btn-outline-danger mt-2">
                        Uitloggen
                    </a>
                </div>
            </div>
        </div>

        <!-- Hoofd content -->
        <div class="col-md-9">

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- TAB: NAW Gegevens -->
            <?php if ($tab === 'naw'): ?>
                <div class="form-card">
                    <h4 class="section-title">NAW Gegevens</h4>
                    <p class="text-muted small mb-4">Houd je gegevens actueel voor een snelle checkout.</p>

                    <form method="POST" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Voornaam *</label>
                                <input type="text" name="voornaam" class="form-control"
                                       value="<?php echo htmlspecialchars($customer['voornaam'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Achternaam *</label>
                                <input type="text" name="achternaam" class="form-control"
                                       value="<?php echo htmlspecialchars($customer['achternaam'] ?? ''); ?>" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Adres (straat + huisnummer)</label>
                                <input type="text" name="adres" class="form-control"
                                       value="<?php echo htmlspecialchars($customer['adres'] ?? ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Postcode</label>
                                <input type="text" name="postcode" class="form-control"
                                       value="<?php echo htmlspecialchars($customer['postcode'] ?? ''); ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Stad / Woonplaats</label>
                                <input type="text" name="stad" class="form-control"
                                       value="<?php echo htmlspecialchars($customer['stad'] ?? ''); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Land</label>
                                <select name="land" class="form-select">
                                    <?php
                                    $landen = ['Nederland','België','Duitsland','Frankrijk','Spanje','Italië','Overig'];
                                    foreach ($landen as $l):
                                        $sel = ($customer['land'] ?? 'Nederland') === $l ? 'selected' : '';
                                    ?>
                                        <option <?php echo $sel; ?>><?php echo $l; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Telefoonnummer</label>
                                <input type="tel" name="telefoon" class="form-control"
                                       value="<?php echo htmlspecialchars($customer['telefoon'] ?? ''); ?>">
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" name="save_naw" class="btn btn-primary px-4">
                                    Opslaan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            <!-- TAB: Wachtwoord wijzigen -->
            <?php elseif ($tab === 'password'): ?>
                <div class="form-card">
                    <h4 class="section-title">Wachtwoord wijzigen</h4>

                    <form method="POST" novalidate style="max-width:420px;">
                        <div class="mb-3">
                            <label class="form-label">Huidig wachtwoord</label>
                            <input type="password" name="huidig_wachtwoord" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nieuw wachtwoord <small class="text-muted">(min. 8 tekens)</small></label>
                            <input type="password" name="nieuw_wachtwoord" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Herhaal nieuw wachtwoord</label>
                            <input type="password" name="bevestig_wachtwoord" class="form-control" required>
                        </div>
                        <button type="submit" name="save_password" class="btn btn-primary px-4">
                            Wachtwoord wijzigen
                        </button>
                    </form>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>