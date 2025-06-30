<?php
require_once '../includes/auth.php';
requireAdmin();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --uvs-blue-dark: #0A2342;
            --uvs-blue: #007BFF;
            --uvs-white: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            margin: 0;
        }

        .sidebar {
            height: 100vh;
            background: var(--uvs-blue-dark);
            color: var(--uvs-white);
            padding: 20px;
            position: fixed;
            width: 220px;
        }

        .sidebar h4 {
            margin-bottom: 2rem;
            font-weight: bold;
            color: var(--uvs-white);
        }

        .sidebar a {
            color: var(--uvs-white);
            display: block;
            padding: 10px 0;
            font-weight: 500;
            text-decoration: none;
        }

        .sidebar a:hover {
            color: var(--uvs-blue);
        }

        .main-content {
            margin-left: 220px;
            padding: 40px;
        }

        .welcome-card {
            background: var(--uvs-white);
            border-radius: 1rem;
            padding: 2rem 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: fadeInUp 1s ease;
        }

        .welcome-card h1 {
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--uvs-blue-dark);
        }

        .welcome-card p {
            font-size: 1.1rem;
            color: #6c757d;
        }

        .nav-icon {
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4><i class="bi bi-speedometer2 nav-icon"></i>Admin</h4>
    <a href="utilisateurs.php"><i class="bi bi-people nav-icon"></i>Étudiants</a>
    <a href="formations.php"><i class="bi bi-journal-bookmark nav-icon"></i>Formations</a>
    <a href="matieres.php"><i class="bi bi-book nav-icon"></i>Matières</a>
    <a href="notes.php"><i class="bi bi-clipboard-check nav-icon"></i>Notes</a>
    <hr class="text-white">
    <a href="../logout.php" class="text-warning"><i class="bi bi-box-arrow-right nav-icon"></i>Déconnexion</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="welcome-card">
        <h1 class="animate__animated animate__fadeInDown">
            Bonjour <?= htmlspecialchars($_SESSION['user']['prenom']) ?> 👋
        </h1>
        <p class="animate__animated animate__fadeInUp animate__delay-1s">
            Bienvenue sur le tableau de bord administrateur. Gérez les utilisateurs, les formations, les matières et les notes depuis cet espace sécurisé.
        </p>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
