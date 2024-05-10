<?php
require_once "../model/function.php";

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_utilisateur"], $_POST["action"])) {
    $idUtilisateur = $_POST["id_utilisateur"];
    $idUtilisateurConnecte = $_SESSION['id_utilisateur'];
    $action = $_POST["action"];

    if ($action === 'follow') {
        if (!sontAmis($bdd, $idUtilisateurConnecte, $idUtilisateur)) {
            addFriend($bdd, $idUtilisateurConnecte, $idUtilisateur);
            echo "Followed";
            ajouterNotification($bdd, $idUtilisateur, $idUtilisateurConnecte, "followers", "Cette personne vous suit");
            ajouterNotification($bdd, $idUtilisateurConnecte, $idUtilisateur, "following", "Vous suivez cette personne");
        } else {
            echo "Already followed";
        }
    } elseif ($action === 'unfollow') {
        removeFriend($bdd, $idUtilisateurConnecte, $idUtilisateur);
        echo "Unfollowed";
    } else {
        echo "Invalid action";
    }
} else {
    echo "Invalid request";
}
?>