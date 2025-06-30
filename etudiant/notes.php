<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

// Récupérer les notes de l'étudiant avec matières et formation
$stmt = $conn->prepare("
    SELECT n.note, m.libelle AS matiere, f.libelle AS formation
    FROM notes n
    JOIN matieres m ON n.matiere_id = m.id
    JOIN formations f ON m.formation_id = f.id
    WHERE n.etudiant_id = ?
    ORDER BY f.libelle, m.libelle
");
$stmt->execute([$userId]);
$notes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Mes Notes</title>

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
            max-width: 800px;
            margin-top: 5rem;
            background: #fff;
            padding: 40px 50px;
            border-radius: 1rem;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
            text-align: center;
        }
        h2 {
            font-weight: 700;
            margin-bottom: 2rem;
            color: #212529;
        }
        table {
            text-align: left;
        }
        p {
            font-size: 1.2rem;
            color: #555;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-dark bg-primary">
    <div class="container-fluid px-4">
        <a href="dashboard.php" class="navbar-brand">Espace Étudiant</a>
        <ul class="navbar-nav ms-auto gap-3">
            <li class="nav-item"><a href="dashboard.php" class="nav-link">Accueil</a></li>
            <li class="nav-item"><a href="profil.php" class="nav-link">Profil</a></li>
            <li class="nav-item"><a href="notes.php" class="nav-link active">Mes Notes</a></li>
            <li class="nav-item"><a href="../logout.php" class="nav-link text-warning fw-bold">Déconnexion</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2>Mes Notes</h2>

    <?php if (empty($notes)): ?>
        <p>Aucune note enregistrée pour le moment.</p>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>Formation</th>
                    <th>Matière</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notes as $n): ?>
                    <tr>
                        <td><?= htmlspecialchars($n['formation']) ?></td>
                        <td><?= htmlspecialchars($n['matiere']) ?></td>
                        <td><?= htmlspecialchars($n['note']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
