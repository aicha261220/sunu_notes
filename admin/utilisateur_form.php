<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$id = $_GET['id'] ?? null;
$isEdit = $id !== null;

// Récupérer les formations pour le select
$stmtForm = $conn->query("SELECT * FROM formations ORDER BY libelle");
$formations = $stmtForm->fetchAll();

if ($isEdit) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'etudiant'");
    $stmt->execute([$id]);
    $etudiant = $stmt->fetch();
    if (!$etudiant) {
        die("Étudiant introuvable");
    }
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $formation_id = $_POST['formation_id'] ?? null;
    $password = $_POST['password'] ?? '';

    if ($nom === '' || $prenom === '' || $email === '' || !$formation_id) {
        $errors[] = "Les champs Nom, Prénom, Email et Formation sont obligatoires.";
    }

    // Vérifier email unique sauf pour modification
    $stmtEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmtEmail->execute([$email, $id ?? 0]);
    if ($stmtEmail->fetch()) {
        $errors[] = "Cet email est déjà utilisé.";
    }

    if (!$isEdit && $password === '') {
        $errors[] = "Le mot de passe est obligatoire à la création.";
    }

    if (empty($errors)) {
        if ($isEdit) {
            // Modifier sans changer le mot de passe si vide
            if ($password !== '') {
                // Stockage mot de passe en clair (à améliorer avec password_hash)
                $stmtUpdate = $conn->prepare("UPDATE users SET nom=?, prenom=?, email=?, telephone=?, adresse=?, formation_id=?, mot_de_passe=? WHERE id=?");
                $stmtUpdate->execute([$nom, $prenom, $email, $telephone, $adresse, $formation_id, $password, $id]);
            } else {
                $stmtUpdate = $conn->prepare("UPDATE users SET nom=?, prenom=?, email=?, telephone=?, adresse=?, formation_id=? WHERE id=?");
                $stmtUpdate->execute([$nom, $prenom, $email, $telephone, $adresse, $formation_id, $id]);
            }
            $success = "Étudiant modifié avec succès.";
        } else {
            // Création, génération du matricule
            $prefix = "ETU" . date('Y');
            $stmtLastMatricule = $conn->prepare("SELECT matricule FROM users WHERE matricule LIKE ? ORDER BY matricule DESC LIMIT 1");
            $stmtLastMatricule->execute([$prefix . '%']);
            $lastMat = $stmtLastMatricule->fetchColumn();

            if ($lastMat) {
                $num = (int)substr($lastMat, 7);
                $num++;
            } else {
                $num = 1;
            }
            $matricule = $prefix . str_pad($num, 4, "0", STR_PAD_LEFT);

            // Stockage mot de passe en clair (à améliorer)
            $stmtInsert = $conn->prepare("INSERT INTO users (nom, prenom, email, mot_de_passe, telephone, adresse, role, matricule, formation_id) VALUES (?, ?, ?, ?, ?, ?, 'etudiant', ?, ?)");
            $stmtInsert->execute([$nom, $prenom, $email, $password, $telephone, $adresse, $matricule, $formation_id]);

            $success = "Étudiant créé avec succès.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title><?= $isEdit ? "Modifier" : "Ajouter" ?> un étudiant</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            margin: 0;
        }
        .navbar {
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
        .nav-link:hover {
            color: #0d6efd;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            background: white;
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
        label.form-label {
            font-weight: 600;
        }
        .btn-primary {
            border-radius: 0.6rem;
            padding: 0.7rem 1.2rem;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0047b3;
        }
        .alert {
            border-radius: 0.5rem;
            font-weight: 500;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-dark bg-primary">
    <div class="container-fluid px-4">
        <a href="dashboard.php" class="navbar-brand">Admin Dashboard</a>
        <ul class="navbar-nav ms-auto gap-3">
            <li class="nav-item"><a href="utilisateurs.php" class="nav-link">Retour aux étudiants</a></li>
            <li class="nav-item"><a href="../logout.php" class="nav-link text-warning fw-bold">Déconnexion</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2><?= $isEdit ? "Modifier" : "Ajouter" ?> un étudiant</h2>

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

    <form method="POST" action="">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom *</label>
            <input type="text" name="nom" id="nom" required class="form-control" value="<?= htmlspecialchars($etudiant['nom'] ?? '') ?>" />
        </div>
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom *</label>
            <input type="text" name="prenom" id="prenom" required class="form-control" value="<?= htmlspecialchars($etudiant['prenom'] ?? '') ?>" />
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <input type="email" name="email" id="email" required class="form-control" value="<?= htmlspecialchars($etudiant['email'] ?? '') ?>" />
        </div>
        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="text" name="telephone" id="telephone" class="form-control" value="<?= htmlspecialchars($etudiant['telephone'] ?? '') ?>" />
        </div>
        <div class="mb-3">
            <label for="adresse" class="form-label">Adresse</label>
            <input type="text" name="adresse" id="adresse" class="form-control" value="<?= htmlspecialchars($etudiant['adresse'] ?? '') ?>" />
        </div>
        <div class="mb-3">
            <label for="formation_id" class="form-label">Formation *</label>
            <select name="formation_id" id="formation_id" required class="form-select">
                <option value="">-- Choisir une formation --</option>
                <?php foreach ($formations as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= (isset($etudiant['formation_id']) && $etudiant['formation_id'] == $f['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label"><?= $isEdit ? "Nouveau mot de passe (laisser vide pour ne pas changer)" : "Mot de passe *" ?></label>
            <input type="password" name="password" id="password" class="form-control" <?= $isEdit ? '' : 'required' ?> />
        </div>

        <button type="submit" class="btn btn-primary"><?= $isEdit ? "Modifier" : "Ajouter" ?></button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
