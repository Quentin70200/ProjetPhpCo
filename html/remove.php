<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/vendor/autoload.php';

require_once('config.php');
session_start();


$conn = new PDO('mysql:host=mysql;dbname='. getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['email'])) {
$token = bin2hex(random_bytes(32)); //génère un jeton aléatoire de 64 caractères hexadécimaux //

// Insère le jeton dans la base de données //

$email = $_POST['email'];
$expiration = date ('Y-m-d H:i:s', strtotime('1 hour')); // expiration d'une heure //
$sql = "INSERT INTO reset_password_token (email, token, expiration) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$email, $token, $expiration]);

// ...................................... //

// Utilisation de PHPMailer pour envoyer l'e-mail //

$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = 'mailhog';
$mail->Port = 1025;
$mail->From = 'blablabla@gmail.fr';
$mail->addAddress($email);
$mail->Subject = 'réinitialisation de mot de passe';
$mail->Body= 'Cliquez sur le lien suivant pour réinitialiser votre mot de passe : ';
$mail->Body .= 'http://localhost:8080/newPassword.php?token=' . $token;

if (!$mail->send()) {
    echo 'Erreur lors de l\'envoi de l\'email : ' . $mail-ErrorInfo;
} else {
    echo 'E-mail envoyé avec succès';
}
}
// .............................................. //

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <title>Formulaire de réinitialisation</title>
</head>
<body>

    <div class="container container d-flex flex-column justify-content-center align-items-center vh-100">
       <div class="bg-body-secondary p-4 w-50">
          <h2>Formulaire de réinitialisation</h2>
           <form action="#" method="post">
              <label for="email" class="mb-2">email</label>
              <input type="email" name="email" class="form-control mb-3" placeholder="exemple@gmail.com" required>
             <div>
              <button class="btn btn-primary">Envoyer</button> 
              <a class="d-inline-block ms-4 align-center" href="login.php">Connexion
              </a>
             </div>
           </form>
       </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
</body>
</html>