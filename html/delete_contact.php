<?php
require_once('config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Vous devez vous connecter pour continuer.";
    header('Location: login.php');
}

if (isset($_GET['contactID'])) {
    $contactID = $_GET['contactID'];
    
    try {
        // Établir une connexion à la base de données
        $conn = new PDO('mysql:host=mysql;dbname=' . getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Préparez la requête DELETE pour supprimer le contact en fonction de son ID
        $sql = "DELETE FROM contacts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$contactID]);
        
        // Vérifiez si la suppression a réussi
        $rowCount = $stmt->rowCount();
        
        if ($rowCount > 0) {
            // La suppression a réussi, renvoyez une réponse de succès
           header("Location: dashbord.php");
        } else {
            // Aucun contact n'a été supprimé, renvoyez un message d'erreur
            $_SESSION['sectionErreur'] = "La suppression a échoué.";
            header("Location: dashbord.php");
        }
    } catch (PDOException $e) {
        // Gérez les erreurs PDO
            $_SESSION['sectionErreur'] = "Erreur de base de données : " .$e->getMessage();
            header("Location: dashbord.php");
    }
} else {
    // L'ID du contact à supprimer n'a pas été fourni, renvoyez un message d'erreur
    $_SESSION['sectionErreur'] = "ID de contact manquant.";
    header("Location: dashbord.php");
}
?>