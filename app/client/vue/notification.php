<?php
require_once "../model/function.php";

verifierConnexion();
$userId = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : '';

$notifications = [];
if ($userId) {
    $infosUtilisateur = chargerInfosUtilisateur($bdd, $userId);
    if ($infosUtilisateur) {
        extract($infosUtilisateur);
        $nonFriends = $infosUtilisateur['nonFriends']; 
        $notifications = getNotificationsUtilisateur($bdd, $userId);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["publicationId"])) {
        $publicationId = $_POST["publicationId"];

        if (isset($_POST["commentaire"])) {
            $commentaire = $_POST["commentaire"];
            $result = traiterCommentairePublication($bdd, $publicationId, $userId, $commentaire);
        } else {
            $result = traiterLikePublication($bdd, $publicationId, $userId);
        }
    }
}
?>  
<?php include "header.php"; ?>
<div class="feed">
    <div class="feed__header">
        <h2><?php $formatNomPrenom = '@' . strtolower($prenom . '_' . $nom);
        echo ' ' . $formatNomPrenom;?></h2>
    </div>     
    <div class="notifications">
        <div class="rass">
            <?php
            if (empty($notifications)) {
                echo '<div class="conteneur-centre">';
                echo '<p "message-centre" >Aucune notification pour l\'instant</p>';
                echo '</div>';
            }
            else{
                foreach ($notifications as $notification) {
                    echo '<div class="notification">';
                    echo '<img src="' . htmlspecialchars($notification['photo_profil']) . '" alt="Avatar" class="user-avatar">';
                    echo '<div class="notification-content">';
                    echo '<p class="notification-text">' . htmlspecialchars($notification['contenu']) . '</p>';
                    echo '<p class="notification-time">Date: ' . htmlspecialchars($notification['date_notification']) . '</p>';
                    echo '</div>';
                    echo '<div class="notification-user">';
                    echo '<p>' . htmlspecialchars($notification['prenom']) . ' ' . htmlspecialchars($notification['nom']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>  
</div>
<?php include "header_end.php"; ?>