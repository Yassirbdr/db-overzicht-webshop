<?php
// ==============================
// partials/navbar.php
// ==============================

require_once __DIR__ . '/../config/auth.php';

// Basis URL voor dit project (belangrijk voor XAMPP!)
$base = '/DB_OVERZICHT';
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--clr-dark);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo $base; ?>/index.php">
            <span style="color: var(--clr-accent);">●</span> Webshop
        </a>

        <button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base; ?>/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base; ?>/pages/products.php">Producten</a>
                </li>

                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base; ?>/pages/profile.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1 mb-1" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.029 10 8 10c-2.03 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                            </svg>
                            Mijn Account
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-sm btn-outline-light px-3" 
                           href="<?php echo $base; ?>/pages/logout.php">
                            Uitloggen
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base; ?>/pages/login.php">Inloggen</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-sm px-3 fw-semibold" 
                           href="<?php echo $base; ?>/pages/register.php"
                           style="background: var(--clr-accent); color: #fff; border-radius: 6px;">
                            Registreren
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>