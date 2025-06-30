<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

// Suppression matière
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = (int)$_GET['id'];
    $stmtDel = $conn->prepare("DELETE FROM matieres WHERE id = ?");
    $stmtDel->execute([$id]);
    header("Location: matieres.php");
    exit;
}

// Ajout/modification
$errors = [];
$success = '';
$editMatiere = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM matieres WHERE id = ?");
    $stmt->execute([$id]);
    $editMatiere = $stmt->fetch();
    if (!$editMatiere) {
        die("Matière introuvable");
    }
}

// Récupérer toutes les formations pour le select
$stmtForm = $conn->query("SELECT * FROM formations ORDER BY libelle");
$formations = $stmtForm->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libelle = trim($_POST['libelle'] ?? '');
    $formation_id = $_POST['formation_id'] ?? null;

    if ($libelle === '' || !$formation_id) {
        $errors[] = "Le libellé et la formation sont obligatoires.";
    }

    if (empty($errors)) {
        if ($editMatiere) {
            $stmt = $conn->prepare("UPDATE matieres SET libelle = ?, formation_id = ? WHERE id = ?");
            $stmt->execute([$libelle, $formation_id, $editMatiere['id']]);
            $success = "Matière modifiée avec succès.";
        } else {
            $stmt = $conn->prepare("INSERT INTO matieres (libelle, formation_id) VALUES (?, ?)");
            $stmt->execute([$libelle, $formation_id]);
            $success = "Matière ajoutée avec succès.";
        }
        header("Location: matieres.php");
        exit;
    }
}

// Récupérer toutes les matières avec formations
$stmt = $conn->query("SELECT m.*, f.libelle AS formation FROM matieres m JOIN formations f ON m.formation_id = f.id ORDER BY m.libelle");
$matieres = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion des Matières - Admin</title>

    <!-- Bootstrap CSS + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet" />

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --uvs-blue-dark: #0A2342;
            --uvs-blue: #007BFF;
            --uvs-white: #ffffff;
            --uvs-yellow: #ffd43b;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            margin: 0;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 220px;
            background-color: var(--uvs-blue-dark);
            color: var(--uvs-white);
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar h4 {
            font-weight: 700;
            margin-bottom: 3rem;
            font-size: 1.8rem;
            letter-spacing: 0.05em;
            user-select: none;
        }
        .sidebar a {
            color: var(--uvs-white);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            transition: color 0.3s ease;
        }
        .sidebar a i {
            margin-right: 12px;
            font-size: 1.25rem;
        }
        .sidebar a:hover,
        .sidebar a.active {
            color: var(--uvs-yellow);
        }

        /* Main content */
        .main-content {
            margin-left: 220px;
            padding: 40px 50px;
            min-height: 100vh;
            background: #f8f9fa;
        }

        /* Page header */
        .page-header {
            font-weight: 700;
            font-size: 2.4rem;
            color: var(--uvs-blue-dark);
            margin-bottom: 2rem;
            user-select: none;
            animation: animate__fadeInDown 0.8s ease forwards;
        }

        /* Form styling */
        form {
            background: var(--uvs-white);
            max-width: 600px;
            margin: 0 auto 3rem;
            padding: 2.5rem 3rem;
            border-radius: 1rem;
            box-shadow: 0 12px 36px rgb(0 0 0 / 0.08);
            animation: animate__fadeInUp 0.8s ease forwards;
        }
        label.form-label {
            font-weight: 600;
            font-size: 1.1rem;
        }
        input.form-control, select.form-select {
            border-radius: 0.6rem;
            padding: 0.7rem 1rem;
            font-size: 1rem;
            box-shadow: inset 0 2px 6px rgb(0 0 0 / 0.06);
            transition: border-color 0.3s ease;
        }
        input.form-control:focus, select.form-select:focus {
            border-color: var(--uvs-blue);
            box-shadow: 0 0 10px rgb(0 123 255 / 0.3);
            outline: none;
        }
        .btn-primary, .btn-secondary {
            border-radius: 0.6rem;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.6rem 2rem;
            box-shadow: 0 8px 22px rgb(0 123 255 / 0.25);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            user-select: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            box-shadow: 0 12px 30px rgb(0 86 179 / 0.5);
        }
        .btn-secondary {
            background-color: #6c757d;
            box-shadow: 0 8px 20px rgb(108 117 125 / 0.25);
            color: white;
        }
        .btn-secondary:hover {
            background-color: #4e555b;
            box-shadow: 0 12px 30px rgb(78 85 91 / 0.5);
        }
        .form-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 1.5rem;
        }

        /* Alerts */
        .alert {
            max-width: 600px;
            margin: 1rem auto 2rem;
            border-radius: 0.6rem;
            font-weight: 500;
            box-shadow: 0 8px 20px rgb(0 0 0 / 0.07);
        }

        /* Table styling */
        .table-container {
            max-width: 900px;
            margin: 0 auto;
            background: var(--uvs-white);
            padding: 2rem 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 14px 48px rgb(0 0 0 / 0.1);
            animation: animate__fadeInUp 1s ease forwards;
        }
        table {
            width: 100%;
            border-collapse: separate !important;
            border-spacing: 0 15px !important;
            font-size: 1rem;
            color: #212529;
        }
        thead tr {
            background-color: var(--uvs-blue);
            color: var(--uvs-white);
            border-radius: 1rem;
        }
        thead th {
            border: none !important;
            padding: 14px 15px !important;
            font-weight: 700;
            user-select: none;
        }
        tbody tr {
            background-color: #f9fbff;
            box-shadow: 0 6px 20px rgb(0 0 0 / 0.05);
            border-radius: 0.9rem;
            transition: transform 0.25s ease, box-shadow 0.3s ease;
        }
        tbody tr:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgb(0 0 0 / 0.12);
            background-color: #e6f0ff;
        }
        tbody td {
            border: none !important;
            padding: 18px 15px !important;
            vertical-align: middle !important;
        }

        /* Actions buttons */
        .btn-sm {
            font-weight: 600;
            border-radius: 0.6rem;
            padding: 7px 14px;
            font-size: 0.95rem;
            transition: box-shadow 0.3s ease;
            user-select: none;
        }
       .btn-primary {
    background-color: var(--uvs-blue);
    border: none;
    box-shadow: 0 6px 18px rgb(0 123 255 / 0.3);
    color: white;
}
.btn-primary:hover {
    background-color: #0056b3; /* bleu UVS foncé */
    box-shadow: 0 10px 28px rgb(0 86 179 / 0.5);
    color: white;
}
.btn-secondary {
    background-color: #6c757d;
    box-shadow: 0 8px 20px rgb(108 117 125 / 0.25);
    color: white;
}
.btn-secondary:hover {
    background-color: #4e555b;
    box-shadow: 0 12px 30px rgb(78 85 91 / 0.5);
    color: white;
}


        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding: 15px 20px;
                flex-direction: row;
                justify-content: space-around;
                gap: 1rem;
            }
            .sidebar h4 {
                display: none;
            }
            .sidebar a {
                margin: 0;
                font-size: 1rem;
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            table {
                font-size: 0.9rem;
            }
            thead tr {
                display: none;
            }
            tbody tr {
                display: block;
                margin-bottom: 1.2rem;
                background-color: var(--uvs-white);
                box-shadow: 0 4px 15px rgb(0 0 0 / 0.1);
                border-radius: 0.7rem;
                padding: 1rem;
            }
            tbody td {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
                border: none !important;
                vertical-align: baseline !important;
            }
            tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--uvs-blue-dark);
            }
            tbody td:last-child {
                justify-content: center;
                gap: 0.7rem;
            }
            .btn-sm {
                font-size: 0.85rem;
                padding: 5px 10px;
            }
            form {
                padding: 1.5rem 1.8rem;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4><i class="bi bi-speedometer2"></i>Admin</h4>
    <a href="utilisateurs.php"><i class="bi bi-people"></i>Étudiants</a>
    <a href="formations.php"><i class="bi bi-journal-bookmark"></i>Formations</a>
    <a href="matieres.php" class="active"><i class="bi bi-book"></i>Matières</a>
    <a href="notes.php"><i class="bi bi-clipboard-check"></i>Notes</a>
    <hr style="border-color: rgba(255,255,255,0.2); margin: 1rem 0;">
    <a href="../logout.php" class="text-warning fw-bold"><i class="bi bi-box-arrow-right"></i>Déconnexion</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <h1 class="page-header">Gestion des Matières</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger animate__animated animate__fadeInDown">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="animate__animated animate__fadeInUp">
        <div class="mb-4">
            <label for="libelle" class="form-label">Libellé de la matière *</label>
            <input type="text" name="libelle" id="libelle" required class="form-control" value="<?= htmlspecialchars($editMatiere['libelle'] ?? '') ?>" />
        </div>
        <div class="mb-4">
            <label for="formation_id" class="form-label">Formation *</label>
            <select name="formation_id" id="formation_id" required class="form-select">
                <option value="">-- Choisir une formation --</option>
                <?php foreach ($formations as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= (isset($editMatiere['formation_id']) && $editMatiere['formation_id'] == $f['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn btn-primary px-5"><?= $editMatiere ? "Modifier" : "Ajouter" ?></button>
            <?php if ($editMatiere): ?>
                <a href="matieres.php" class="btn btn-secondary px-5">Annuler</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Formation</th>
                    <th style="width:140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($matieres)): ?>
                    <tr><td colspan="3" class="text-center py-4">Aucune matière trouvée.</td></tr>
                <?php else: ?>
                    <?php foreach ($matieres as $m): ?>
                        <tr>
                            <td data-label="Matière"><?= htmlspecialchars($m['libelle']) ?></td>
                            <td data-label="Formation"><?= htmlspecialchars($m['formation']) ?></td>
                            <td data-label="Actions">
                                <a href="matieres.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-primary">Modifier</a>
                                <a href="matieres.php?action=delete&id=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
