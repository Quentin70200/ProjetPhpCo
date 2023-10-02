<?php
try {
    $conn = new PDO('mysql:host=mysql;dbname='. getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
    // Configuration pour afficher les erreurs PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}

// Étape 2 : Préparation de la requête
if ($conn) {
    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE champ = :valeur");
    // Vous pouvez maintenant utiliser $stmt pour exécuter la requête SQL
} else {
    echo "La connexion à la base de données a échoué.";
}
?>
