<?php
// ==============================
// config/auth.php  –  sessie & hulpfuncties
// ==============================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['customer_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header("Location: /pages/login.php");
        exit;
    }
}

function currentCustomerId(): ?int {
    return $_SESSION['customer_id'] ?? null;
}

function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}