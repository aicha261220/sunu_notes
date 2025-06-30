<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

// Suppression
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = (int)$_GET['id'];
    $stmtDel = $conn->prepare("DELETE FROM formations WHERE id = ?");
    $stmtDel->execute([$id]);
    header("Location: formations.php");
    exit;
}

// Modification
$errors = [];
$editFormation = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM formations WHERE id = ?");
    $stmt->execute([$id]);
    $editFormation = $stmt->fetch();
    if (!$editFormation) {
        die("Formation introuvable");
    }
}

// Ajout ou mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libelle = trim($_POST['libelle'] ?? '');
    if ($libelle === '') {
        $errors[] = "Le libellé est obligatoire.";
    }

    if (!$errors) {
        if ($editFormation) {
            $stmt = $conn->prepare("UPDATE formations SET libelle = ? WHERE id = ?");
            $stmt->execute([$libelle, $editFormation['id']]);
        } else {
            $stmt = $conn->prepare("INSERT INTO formations (libelle) VALUES (?)");
            $stmt->execute([$libelle]);
        }
        header("Location: formations.php");
        exit;
    }
}

$stmt = $conn->query("SELECT * FROM formations ORDER BY libelle");
$formations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Formations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f1f3f6;
        }

        .navbar {
            background-color: #0A2342;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .nav-link.active, .nav-link:hover {
            color: #ffd43b !important;
        }

        .container {
            margin-top: 50px;
            max-width: 700px;
        }

        h2 {
            font-weight: bold;
            color: #0A2342;
            margin-bottom: 30px;
        }

        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.07);
        }

        .form-label {
            font-weight: 600;
        }

        .btn-primary {
            background-color: #0A2342;
            border: none;
        }

        .btn-primary:hover {
            background-color: #052659;
        }

        .btn-sm {
            font-size: 0.85rem;
            padding: 0.4rem 0.75rem;
            border-radius: 0.4rem;
        }

        .table thead {
            background-color: #0A2342;
            color: white;
        }

        .table tbody tr {
            animation: fadeIn 0.4s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="dashboard.php">Admin</a>
        <ul class="navbar-nav ms-auto gap-3">
            <li class="nav-item"><a href="formations.php" class="nav-link active">Formations</a></li>
            <li class="nav-item"><a href="utilisateurs.php" class="nav-link">Étudiants</a></li>
            <li class="nav-item"><a href="matieres.php" class="nav-link">Matières</a></li>
            <li class="nav-item"><a href="notes.php" class="nav-link">Notes</a></li>
            <li class="nav-item"><a href="../logout.php" class="nav-link text-warning fw-bold">Déconnexion</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2 class="text-center"><i class="bi bi-mortarboard-fill me-2"></i>Gestion des Formations</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="form-container mb-4">
        <form method="POST">
            <div class="mb-3">
                <label for="libelle" class="form-label">Libellé de la formation *</label>
                <input type="text" name="libelle" id="libelle" class="form-control" required value="<?= htmlspecialchars($editFormation['libelle'] ?? '') ?>" />
            </div>
            <div class="d-flex justify-content-center gap-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i><?= $editFormation ? "Modifier" : "Ajouter" ?>
                </button>
                <?php if ($editFormation): ?>
                    <a href="formations.php" class="btn btn-secondary px-4"><i class="bi bi-x-circle me-1"></i>Annuler</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($formations)): ?>
                    <tr><td colspan="2">Aucune formation trouvée.</td></tr>
                <?php else: ?>
                    <?php foreach ($formations as $f): ?>
                        <tr>
                            <td><?= htmlspecialchars($f['libelle']) ?></td>
                            <td>
                                <a href="formations.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="formations.php?action=delete&id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette formation ?')">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
