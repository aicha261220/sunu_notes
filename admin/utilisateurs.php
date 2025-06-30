<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

// Suppression
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = (int)$_GET['id'];
    $stmtDel = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'etudiant'");
    $stmtDel->execute([$id]);
    header("Location: utilisateurs.php");
    exit;
}

// Récupérer tous les étudiants
$stmt = $conn->prepare("SELECT u.*, f.libelle AS formation FROM users u LEFT JOIN formations f ON u.formation_id = f.id WHERE u.role = 'etudiant' ORDER BY u.nom");
$stmt->execute();
$etudiants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion Étudiants</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
            margin: 0;
        }

        .navbar {
            background-color: #0A2342;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .nav-link:hover, .nav-link.active {
            color: #ffd43b !important;
        }

        .container {
            padding: 40px;
        }

        h2 {
            font-weight: 700;
            color: #0A2342;
        }

        .btn-success {
            background-color: #007BFF;
            border: none;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-success:hover {
            background-color: #0056b3;
        }

        .table {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background-color: #0A2342;
            color: white;
        }

        .table td, .table th {
            vertical-align: middle !important;
        }

        .btn-sm {
            font-size: 0.9rem;
        }

        tbody tr {
            animation: fadeInUp 0.5s ease-in;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Admin</a>
        <ul class="navbar-nav ms-auto gap-3">
            <li class="nav-item"><a class="nav-link active" href="utilisateurs.php">Étudiants</a></li>
            <li class="nav-item"><a class="nav-link" href="formations.php">Formations</a></li>
            <li class="nav-item"><a class="nav-link" href="matieres.php">Matières</a></li>
            <li class="nav-item"><a class="nav-link" href="notes.php">Notes</a></li>
            <li class="nav-item"><a class="nav-link text-warning fw-bold" href="../logout.php">Déconnexion</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people-fill me-2"></i>Liste des étudiants</h2>
        <a href="utilisateur_form.php" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i>Ajouter</a>
    </div>

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Adresse</th>
                    <th>Formation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($etudiants)): ?>
                    <?php foreach ($etudiants as $etu): ?>
                        <tr>
                            <td><?= htmlspecialchars($etu['matricule']) ?></td>
                            <td><?= htmlspecialchars($etu['nom']) ?></td>
                            <td><?= htmlspecialchars($etu['prenom']) ?></td>
                            <td><?= htmlspecialchars($etu['email']) ?></td>
                            <td><?= htmlspecialchars($etu['telephone']) ?></td>
                            <td><?= htmlspecialchars($etu['adresse']) ?></td>
                            <td><?= htmlspecialchars($etu['formation']) ?></td>
                            <td>
                                <a href="utilisateur_form.php?id=<?= $etu['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="utilisateurs.php?action=delete&id=<?= $etu['id'] ?>"
                                   onclick="return confirm('Confirmer la suppression ?')"
                                   class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center py-4">Aucun étudiant trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
