<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bienvenue - UVS</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

    * {
      box-sizing: border-box;
    }

    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to bottom right, #0A2342, #007BFF);
      display: flex;
      flex-direction: column;
    }

    header {
      display: flex;
      align-items: center;
      padding: 20px 30px;
      background-color: rgba(255, 255, 255, 0.1);
      position: absolute;
      top: 0;
      width: 100%;
    }

    header img {
      height: 60px;
    }

    .hero {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: var(--uvs-white);
      text-align: center;
      padding: 100px 20px 20px;
    }

    .hero h1 {
      font-size: 3.2rem;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .hero p {
      font-size: 1.2rem;
      max-width: 600px;
      margin-bottom: 30px;
    }

    .btn-custom {
      background-color: var(--uvs-white);
      color: var(--uvs-blue);
      padding: 12px 30px;
      border-radius: 30px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: 0.3s ease;
      border: none;
    }

    .btn-custom:hover {
      background-color: var(--uvs-blue);
      color: var(--uvs-white);
    }

    footer {
      background-color: rgba(0, 0, 0, 0.1);
      text-align: center;
      padding: 15px;
      font-size: 0.9rem;
      color: var(--uvs-white);
    }

    @media screen and (max-width: 768px) {
      .hero h1 {
        font-size: 2.2rem;
      }

      header {
        justify-content: center;
      }
    }
  </style>
</head>
<body>

  <!-- Logo -->
  <header>
    <img src="includes/logo.png.png" alt="Logo UVS">
  </header>

  <!-- Section principale -->
  <div class="hero">
    <h1 class="animate__animated animate__fadeInDown">Bienvenue sur le Portail Étudiant</h1>
    <p class="animate__animated animate__fadeInUp animate__delay-1s">
      Consultez vos résultats, gérez votre profil et accédez à toutes vos informations universitaires en un clic.
    </p>
    <a href="login.php" class="btn btn-custom animate__animated animate__fadeInUp animate__delay-2s">
      Se connecter
    </a>
  </div>

  <!-- Footer -->
  <footer>
    © <?= date("Y") ?> - Université Numérique Cheikh Hamidou Kane (UVS) | Développé par <a href="#" style="color:#fff; text-decoration:underline;">Votre Équipe</a>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
