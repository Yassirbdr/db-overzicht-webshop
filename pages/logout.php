<?php
// ==============================
// pages/logout.php
// ==============================

// Include auth (starts session)
require_once __DIR__ . '/../config/auth.php';

// Destroy session
session_unset();
session_destroy();

// Delete session cookie
setcookie(session_name(), '', time() - 3600, '/');

// Redirect to homepage (FIXED PATH)
header("Location: /DB_OVERZICHT/index.php");
exit;