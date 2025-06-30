<?php
session_start();
require 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $password === $user['mot_de_passe']) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'role' => $user['role'],
            'email' => $user['email'],
            'matricule' => $user['matricule'] ?? null,
        ];

        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: etudiant/dashboard.php");
        }
        exit;
    } else {
        $error = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Connexion - Portail Étudiant</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <style>
        :root {
            --uvs-blue-dark: #0A2342;
            --uvs-blue: #007BFF;
            --uvs-white: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom right, var(--uvs-blue-dark), var(--uvs-blue));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            background-color: var(--uvs-white);
        }

        .card-title {
            font-weight: 600;
            color: var(--uvs-blue-dark);
        }

        .form-control {
            border-radius: 0.6rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }

        .btn-primary {
            border-radius: 0.7rem;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 1.1rem;
            background-color: var(--uvs-blue);
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert-danger {
            border-radius: 0.5rem;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="card shadow p-4">
    <h4 class="card-title mb-4 text-center">Connexion</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input
                type="email"
                name="email"
                id="email"
                required
                class="form-control"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                placeholder="exemple@domaine.com"
            />
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Mot de passe</label>
            <input
                type="password"
                name="password"
                id="password"
                required
                class="form-control"
                placeholder="Votre mot de passe"
            />
        </div>

        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
