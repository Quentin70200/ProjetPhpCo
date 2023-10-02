<?php
require_once('config.php');
session_start();

// Étape 1 : Connexion à la base de données MySQL en utilisant PDO
$conn = new PDO('mysql:host=mysql;dbname='. getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD')); 
// Configuration pour afficher les erreurs PDO
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$token = $_GET['token'];

$currentTimestamp = date('Y-m-d H:i:s');
// Vérifiez le jeton dans la base de données avant de permettre la réinitialisation
$sql = "SELECT email FROM reset_password_token WHERE token = ? AND expiration > ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$token, $currentTimestamp]);


$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    // Affichez un message d'erreur ou redirigez vers une page d'erreur
    echo "Jeton invalide. Veuillez réessayer.";
} else {
    // Le jeton est valide, affichez le formulaire de réinitialisation
    if (isset($_POST['newPassword'])) {
        $newPassword = $_POST['newPassword'];
        // Assurez-vous de valider les champs du mot de passe ici

        // Mise à jour du mot de passe dans la base de données
        $sql = "UPDATE utilisateur SET password_hash = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $row['email']]);

        // Redirige l'utilisateur vers une page de confirmation ou de connexion
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <title>Nouveau mot de passe</title>
</head>
<body class="vh-100 bg-dark-subtle">
  <div class="container d-flex flex-column justify-content-center align-items-center vh-100">
    <div class="bg-body-secondary p-4 w-50">
       <div>
         <h2 class="mb-4 fs-3">Réinitialisation de votre mot de passe</h2>
       </div>
        <form action="<?php if(isset($_GET["token"])) echo "?token=" .$_GET["token"]; ?>" method="POST">

           <div class="form-group">
              <label for="nouveauMotDePasse" class="form-label">Nouveau mot de passe</label>
              <input type="password" class="form-control" placeholder="Entrez le mot de passe" name="newPassword" id="newPasword">
           </div>

           <div class="form-group">
            <label for="encoreUneFois" class="form-label mt-3">Encore une fois</label>
            <input type="password" class="form-control" placeholder="Une dernière fois" name="repeat">
           </div>

           <button type="submit" class="btn btn-primary mt-3">Valider</button>

        </form>
    </div>    
  </div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
</body>
</html>