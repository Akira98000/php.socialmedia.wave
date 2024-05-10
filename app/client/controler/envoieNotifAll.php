<?php

require_once '../model/function.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    
    $nonAdminUsers = getNonAdminUsers($bdd);
    foreach($nonAdminUsers as $user) {
        ajouterNotification($bdd, $user['id_utilisateur'], $_SESSION['id_utilisateur'], 'admin', $message);
    }
    
    echo "Notifications envoyées à tous les utilisateurs non-admin.";
}
?>
