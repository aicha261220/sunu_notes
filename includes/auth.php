<?php
session_start();

function isLogged() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isLogged() && $_SESSION['user']['role'] === 'admin';
}

function isEtudiant() {
    return isLogged() && $_SESSION['user']['role'] === 'etudiant';
}

function requireLogin() {
    if (!isLogged()) {
        header('Location: ../login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('HTTP/1.1 403 Forbidden');
        exit("Accès interdit");
    }
}

function requireEtudiant() {
    requireLogin();
    if (!isEtudiant()) {
        header('HTTP/1.1 403 Forbidden');
        exit("Accès interdit");
    }
}
