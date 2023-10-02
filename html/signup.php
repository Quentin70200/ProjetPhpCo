<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn = new PDO('mysql:host=mysql;dbname=' . getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupère et sécurise les données du formulaire
        $last_name = htmlspecialchars($_POST['nom']);
        $first_name = htmlspecialchars($_POST['prenom']);
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];
        $password_repeat = $_POST['password_repeat'];

        // Vérifie si l'email existe déjà
        $query = "SELECT id FROM utilisateur WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            echo "Cet email est utilisé. Veuillez en choisir un autre.";
        } elseif ($password !== $password_repeat) {
            echo "Les mots de passe ne correspondent pas.";
        } else {
            // Hacher le mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insérer les données dans la base de données
            $query = "INSERT INTO utilisateur (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$first_name, $last_name, $email, $hashed_password]);

            if ($stmt->rowCount() > 0) {
                // Enregistrez un indicateur de réussite dans la session
                $_SESSION['inscription_reussie'] = true;
                header("Location: login.php");
            } else {
                echo "Échec de l'inscription : " . $stmt->errorInfo()[2];
            }
        }

        // Fermeture de la base de données
        $conn = null;
    } catch (PDOException $e) {
        die("Erreur PDO : " . $e->getMessage());
    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Inscription</title>
    <!-- Inclure les styles Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<body>

<div class="container mt-5">

    <h2>Formulaire d'Inscription</h2>

    <form action="#" method="POST">
        <!-- Champ : Prénom -->
        <div class="mb-3">
            <label for="prenom">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required>
        </div>

        <!-- Champ : Nom -->
        <div class="mb-3">
            <label for="nom">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>

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

        <!-- Champ : Répéter le mot de passe -->
        <div class="mb-3">
            <label for="password_repeat">Répéter le mot de passe</label>
            <input type="password" class="form-control" id="password_repeat" name="password_repeat" required>
        </div>
        <div class="mb-3 d-flex align-items-center">
        <!-- Bouton d'envoi -->
        <button type="submit" class="btn btn-primary">S'inscrire</button> <a class="d-inline-block ms-4" href="login.php">Login</a>
        </div>
    </form>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>

</body>
</html>
