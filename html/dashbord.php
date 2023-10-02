<?php

require_once('config.php');
// Démarre la session (nécessaire pour utiliser $_SESSION)
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}



// Étape 1 : Connexion à la base de données MySQL en utilisant PDO
$conn = new PDO('mysql:host=mysql;dbname='. getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD')); 
// Configuration pour afficher les erreurs PDO
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $first_name = $_POST["prenom"];
    $last_name = $_POST["nom"];
    $email = $_POST["email"];

    // vérifie i l'adresse email existe déjà
    $sqlCheck = "SELECT COUNT(*) FROM contacts WHERE user_id = ? AND email = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->execute([$user_id, $email]);
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        $errorMessage = "Cette adresse e-mail est déjà dans vos contacts.";
    } else {
        // L'adresse email est unique, insérer les données dans la base de données
        $sqlInsert = "INSERT INTO contacts (user_id, first_name, last_name, email) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->execute([$user_id, $first_name, $last_name, $email]);
        

        $first_name = "";
        $last_name = "";
        $email = "";
    }
}
    
    $user_id = $_SESSION["user_id"];
    $sql = "SELECT * FROM contacts WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Contacts</title>
    <!-- Inclure jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Inclure les styles Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand ms-4" href="#">Mon Tableau de Bord</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse d-flex justify-content-end" id="navbarNav">
        <ul class="navbar-nav ml-auto">            
            <li class="nav-item  ml-auto">
                <a class="nav-link me-4" href="logout.php">Se déconnecter</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Tableau de Bord - Gestion des Contacts</h2>
    <p>Bienvenue dans votre tableau de bord de gestion des contacts. Vous pouvez ajouter, modifier ou supprimer des contacts ici.</p>
    <div class="row">
        <div class="col-md-6">
            <!-- Formulaire d'ajout de contact -->
            <form method="POST" id="contactForm">
                <div class="form-group mb-3">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                    <div class="invalid-feedback">Ce champ ne peut être vide.</div>
                </div>
                <div class="form-group mb-3">
                    <label for="prenom">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                    <div class="invalid-feedback">Ce champ ne peut être vide.</div>
                </div>
                <div class="form-group mb-3">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback">Ce champ ne peut être vide.</div>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter Contact</button>
            </form> 
        </div>
        <div class="col-md-6 text-decoration-none">
            <!-- Liste des contacts -->
            <?php
                echo "<h2>Liste des contacts</h2>";
                echo "<ul class='list-group list-unstyled'>";

                if (isset($contacts) && is_array($contacts)) {
                    foreach ($contacts as $contact) {
                        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                        {$contact['first_name']} {$contact['last_name']} - {$contact['email']}
                        <a href=\"delete_contact.php?contactID={$contact['id']}\" class='btn btn-danger ml-auto'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
                                <path d='M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z'/>
                            </svg>
                        </a>
                    </li>";
                    }
                } else {
                    echo "<li>Aucun contact à afficher.</li>";
                }
                echo "</ul>";

                if (isset($_SESSION['sectionErreur'])) {
                    echo '<div class="alert alert-danger" role="alert">';
                    echo $_SESSION['sectionErreur'];
                    echo '</div>';
                    // efface la variable de session après l'affichage
                    unset($_SESSION['sectionErreur']);
                }

                echo "<div id='confirmationMessage' class='alert alert-success' style='display: none;'>
                       Le contact a été supprimé avec succès.</div>";
            ?>
        </div>
    </div>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
<script src="index.js"></script>
</body>
</html>
