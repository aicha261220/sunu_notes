<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Étudiant</title>
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
        }

        .sidebar a {
            color: var(--uvs-white);
            display: block;
            padding: 10px 0;
            font-weight: 500;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
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
            text-align: center;
        }

        .welcome-card h1 {
            font-weight: 700;
            color: var(--uvs-blue-dark);
            margin-bottom: 1rem;
        }

        .welcome-card p {
            font-size: 1.1rem;
            color: #6c757d;
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
    <h4><i class="bi bi-person-circle me-2"></i>Étudiant</h4>
    <a href="dashboard.php" class="active"><i class="bi bi-house-door-fill me-2"></i>Accueil</a>
    <a href="profil.php"><i class="bi bi-person-lines-fill me-2"></i>Mon Profil</a>
    <a href="notes.php"><i class="bi bi-journal-check me-2"></i>Mes Notes</a>
    <hr class="text-white">
    <a href="../logout.php" class="text-warning"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a>
</div>

<!-- Main -->
<div class="main-content">
    <div class="welcome-card">
        <h1 class="animate__animated animate__fadeInDown">
            Bienvenue, <?= htmlspecialchars($_SESSION['user']['prenom']) ?> 👋
        </h1>
        <p class="animate__animated animate__fadeInUp animate__delay-1s">
            Vous êtes connecté à votre espace étudiant. Accédez à vos informations à partir du menu à gauche.
        </p>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
