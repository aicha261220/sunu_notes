<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

// Récupérer les données de l'étudiant
$stmt = $conn->prepare("
    SELECT u.*, f.libelle AS formation 
    FROM users u
    LEFT JOIN formations f ON u.formation_id = f.id
    WHERE u.id = ?
");
$stmt->execute([$userId]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    die("Étudiant introuvable.");
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation email
    if ($email === '') {
        $errors[] = "L'email est obligatoire.";
    } else {
        // Vérifier email unique sauf pour soi-même
        $stmtEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmtEmail->execute([$email, $userId]);
        if ($stmtEmail->fetch()) {
            $errors[] = "Cet email est déjà utilisé.";
        }
    }

    // Si mot de passe changé, vérifier les champs
    if ($newPassword !== '' || $confirmPassword !== '') {
        if ($currentPassword === '') {
            $errors[] = "Le mot de passe actuel est requis pour changer de mot de passe.";
        } else {
            // Ici tu compares en clair — idéalement, tu devrais stocker et comparer des hash !
            if ($currentPassword !== $etudiant['mot_de_passe']) {
                $errors[] = "Le mot de passe actuel est incorrect.";
            }
        }
        if ($newPassword !== $confirmPassword) {
            $errors[] = "La confirmation du nouveau mot de passe ne correspond pas.";
        }
        if (strlen($newPassword) < 6) {
            $errors[] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
        }
    }

    if (empty($errors)) {
        // Mise à jour email
        $stmtUpdate = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmtUpdate->execute([$email, $userId]);

        // Mise à jour mot de passe si demandé (en clair)
        if ($newPassword !== '') {
            $stmtPwd = $conn->prepare("UPDATE users SET mot_de_passe = ? WHERE id = ?");
            $stmtPwd->execute([$newPassword, $userId]);
        }

        // Mettre à jour la session email
        $_SESSION['user']['email'] = $email;

        $success = "Profil mis à jour avec succès.";

        // Recharger données
        $stmt->execute([$userId]);
        $etudiant = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Profil Étudiant</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            margin: 0;
        }
        .navbar {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .nav-link {
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: #0d6efd;
        }
        .container {
            max-width: 600px;
            margin-top: 4rem;
            background: #fff;
            padding: 30px 40px;
            border-radius: 1rem;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
        }
        h2 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #212529;
            text-align: center;
        }
        table {
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-dark bg-primary">
    <div class="container-fluid px-4">
        <a href="dashboard.php" class="navbar-brand">Espace Étudiant</a>
        <ul class="navbar-nav ms-auto gap-3">
            <li class="nav-item"><a href="profil.php" class="nav-link active">Profil</a></li>
            <li class="nav-item"><a href="notes.php" class="nav-link">Mes Notes</a></li>
            <li class="nav-item"><a href="../logout.php" class="nav-link text-warning fw-bold">Déconnexion</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2>Profil</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <tr><th>Matricule</th><td><?= htmlspecialchars($etudiant['matricule']) ?></td></tr>
        <tr><th>Nom</th><td><?= htmlspecialchars($etudiant['nom']) ?></td></tr>
        <tr><th>Prénom</th><td><?= htmlspecialchars($etudiant['prenom']) ?></td></tr>
        <tr><th>Téléphone</th><td><?= htmlspecialchars($etudiant['telephone']) ?></td></tr>
        <tr><th>Adresse</th><td><?= htmlspecialchars($etudiant['adresse']) ?></td></tr>
        <tr><th>Formation</th><td><?= htmlspecialchars($etudiant['formation'] ?? '-') ?></td></tr>
    </table>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <input type="email" name="email" id="email" required class="form-control" value="<?= htmlspecialchars($etudiant['email']) ?>" />
        </div>

        <hr />
        <h5>Modifier le mot de passe</h5>
        <div class="mb-3">
            <label for="current_password" class="form-label">Mot de passe actuel</label>
            <input type="password" name="current_password" id="current_password" class="form-control" />
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Nouveau mot de passe</label>
            <input type="password" name="new_password" id="new_password" class="form-control" />
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmer nouveau mot de passe</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" />
        </div>

        <button type="submit" class="btn btn-primary w-100">Mettre à jour</button>
    </form>
</div>
<footer>
    © <?= date('Y') ?> Plateforme de consultation de notes – Projet UVS | Développé par Groupe sunu notes
  </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
