<?php
require_once('config.php');
session_start();

// Étape 4 : Connexion à la base de données
$conn = new PDO('mysql:host=mysql;dbname='. getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Étape 5 : Récupération des contacts depuis la base de données
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

   $email = htmlspecialchars($_POST['email']);
   $password = htmlspecialchars($_POST['password']);

   $query = "SELECT * FROM utilisateur WHERE email = ?";
   $stmt = $conn->prepare($query);
   $stmt->execute([$email]);

   if ($stmt->rowCount() > 0) {
       $row = $stmt->fetch(PDO::FETCH_ASSOC);
       if (password_verify($password, $row['password_hash'])) {
          
        // Vérifie si la case "Se souvenir de moi" a été cochée
            if (isset($_POST['rememberMe'])) {

                // Crée un cookie avec l'identifiant de l'utilisateur
                // Expiration du cookie après une période de temps
                $expire = time() + 3600 * 24 * 30; //30 jours
                setcookie('rememberMeCookie', $row["id"], $expire, '/');
            }


           $_SESSION['email'] = $email;
           $_SESSION['user_id'] = $row["id"];
           header("Location: dashbord.php");
           exit();
        } else {
            echo "Mot de passe incorrect.";
        } 
    } else {
       echo "Aucun utilisateur trouvé avec cet email.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Connexion</title>
    <!-- Inclure les styles Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand ms-4" href="#">Mon Site</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse d-flex justify-content-end me-4" id="navbarNav">
        <ul class="navbar-nav ml-auto">            
            <li class="nav-item">
                <a class="nav-link" href="signup.php">S'inscrire</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Formulaire de Connexion</h2>
    <form action="login.php" method="POST">
        <!-- Champ : Adresse e-mail -->
        <div class="mb-3">
            <label for="email">Adresse e-mail</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <!-- Champ : Mot de passe -->
        <div class="mb-3">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <!-- Option : Se souvenir de moi -->
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
            <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
        </div>

        <!-- Lien : Mot de passe oublié -->
        <div class="mb-3">
            <a href="remove.php">Mot de passe oublié ?</a>
        </div>

        <!-- Bouton d'envoi -->
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
</body>
</html>
