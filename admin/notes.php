<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

// Supprimer une note
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = (int)$_GET['id'];
    $stmtDel = $conn->prepare("DELETE FROM notes WHERE id = ?");
    $stmtDel->execute([$id]);
    header("Location: notes.php");
    exit;
}

// Récupération des notes avec infos liées
$stmt = $conn->query("
    SELECT n.id, n.note, u.nom, u.prenom, m.libelle AS matiere 
    FROM notes n 
    JOIN users u ON n.etudiant_id = u.id 
    JOIN matieres m ON n.matiere_id = m.id 
    ORDER BY u.nom, m.libelle
");
$notes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion des Notes - Admin</title>

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

        /* Header section */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header-section h2 {
            font-weight: 700;
            color: var(--uvs-blue-dark);
            font-size: 2.2rem;
            user-select: none;
            animation: animate__fadeInDown 0.7s ease forwards;
        }

        .btn-add-note {
            background-color: var(--uvs-blue);
            color: var(--uvs-white);
            font-weight: 600;
            border-radius: 0.6rem;
            padding: 0.6rem 1.4rem;
            font-size: 1rem;
            box-shadow: 0 8px 18px rgb(0 123 255 / 0.3);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            user-select: none;
            animation: animate__fadeInRight 0.7s ease forwards;
        }
        .btn-add-note:hover {
            background-color: #0056b3;
            box-shadow: 0 12px 28px rgb(0 86 179 / 0.5);
            text-decoration: none;
            color: #fff;
        }

        /* Table */
        .table-responsive {
            border-radius: 1rem;
            box-shadow: 0 12px 40px rgb(0 0 0 / 0.05);
            background: var(--uvs-white);
            padding: 20px;
        }

        table {
            border-collapse: separate !important;
            border-spacing: 0 12px !important;
            width: 100%;
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
            padding: 12px 15px !important;
            font-weight: 700;
            user-select: none;
        }

        tbody tr {
            background-color: #f9fbff;
            box-shadow: 0 2px 10px rgb(0 0 0 / 0.04);
            border-radius: 0.7rem;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }
        tbody tr:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgb(0 0 0 / 0.1);
            background-color: #e6f0ff;
        }
        tbody td {
            border: none !important;
            padding: 16px 15px !important;
            vertical-align: middle !important;
        }

        /* Action buttons */
        .btn-sm {
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 6px 12px;
            font-size: 0.9rem;
            transition: box-shadow 0.3s ease;
            user-select: none;
        }
        .btn-primary {
            background-color: var(--uvs-blue);
            border: none;
            box-shadow: 0 5px 15px rgb(0 123 255 / 0.3);
        }
        .btn-primary:hover {
            background-color: #0056b3;
            box-shadow: 0 8px 28px rgb(0 86 179 / 0.5);
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
            box-shadow: 0 5px 15px rgb(220 53 69 / 0.3);
        }
        .btn-danger:hover {
            background-color: #a71d2a;
            box-shadow: 0 8px 28px rgb(167 29 42 / 0.5);
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
                display: none; /* For mobile, could add a mobile-friendly table alternative if needed */
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
                padding: 0.4rem 0;
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
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4><i class="bi bi-speedometer2"></i>Admin</h4>
    <a href="utilisateurs.php"><i class="bi bi-people"></i>Étudiants</a>
    <a href="formations.php"><i class="bi bi-journal-bookmark"></i>Formations</a>
    <a href="matieres.php"><i class="bi bi-book"></i>Matières</a>
    <a href="notes.php" class="active"><i class="bi bi-clipboard-check"></i>Notes</a>
    <hr style="border-color: rgba(255,255,255,0.2); margin: 1rem 0;">
    <a href="../logout.php" class="text-warning fw-bold"><i class="bi bi-box-arrow-right"></i>Déconnexion</a>
</div>

<!-- Main content -->
<div class="main-content">
    <div class="header-section">
        <h2 class="animate__animated animate__fadeInDown">Liste des notes</h2>
        <a href="note_form.php" class="btn-add-note animate__animated animate__fadeInRight">
            <i class="bi bi-plus-circle me-2"></i>Ajouter une note
        </a>
    </div>

    <div class="table-responsive animate__animated animate__fadeInUp">
        <table>
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Matière</th>
                    <th>Note</th>
                    <th style="width: 130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($notes)): ?>
                    <?php foreach ($notes as $n): ?>
                        <tr>
                            <td data-label="Étudiant"><?= htmlspecialchars($n['prenom'] . ' ' . $n['nom']) ?></td>
                            <td data-label="Matière"><?= htmlspecialchars($n['matiere']) ?></td>
                            <td data-label="Note"><?= htmlspecialchars($n['note']) ?></td>
                            <td data-label="Actions">
                                <a href="note_form.php?id=<?= $n['id'] ?>" class="btn btn-sm btn-primary">Modifier</a>
                                <a href="notes.php?action=delete&id=<?= $n['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center py-4">Aucune note enregistrée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
