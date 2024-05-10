<?php
require_once "../model/function.php";

verifierConnexion();
$userId = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : '';

if ($userId) {
    $infosUtilisateur = chargerInfosUtilisateur($bdd, $userId);
    if ($infosUtilisateur) {
        extract($infosUtilisateur);
        $nonFriends = $infosUtilisateur['nonFriends']; 
        $followers = getFollowing($bdd, $userId);
    }
}

?>  
<?php include "header.php"; ?>
<script>
     document.addEventListener('DOMContentLoaded', function() {
    const followButtons = document.querySelectorAll('.subscribe-button');

    followButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();

            const userId = this.getAttribute('data-userid');
            const action = this.textContent === "S'abonner" ? 'follow' : 'unfollow';

            const formData = new FormData();
            formData.append('id_utilisateur', userId);
            formData.append('action', action); 

            fetch('../controler/followUser.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === "Followed") {
                    this.textContent = "Se désabonner";
                } else if (data === "Unfollowed") {
                    this.textContent = "S'abonner";
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});

</script>

<div class="feed">
    <div class="feed__header">
        <h2><?php $formatNomPrenom = '@' . strtolower($prenom . '_' . $nom);
        echo ' '.$formatNomPrenom; ?></h2>
    </div>     
    <div class="search-container">
            <div class="search-header">
                <h2>Suggestion pour vous</h2>
            </div>
            <div class="rass">
            <?php
            if (!empty($nonFriends)) {
                foreach ($nonFriends as $user) {
                    echo '<div class="result-item">';
                    echo '<img src="' . htmlspecialchars($user['photo_profil']) . '" alt="Profil" class="result-avatar">';
                    echo '<div class="result-content">';
                    echo '<h3>' . htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']) . '</h3>';
                    echo '<p>' . htmlspecialchars($user['bio']) . '</p>';
                    echo '</div>';
                    echo '<button class="subscribe-button" data-userid="' . htmlspecialchars($user['id_utilisateur']) . '">S\'abonner</button>';
                    echo '</div>';
                }
            } else {
                echo '<p>Pas de suggestions disponibles.</p>';
            }
            ?>
            </div>
        </div>
        <div class="search-container">
            <div class="search-header">
                <h2>Mes following</h2>
            </div>
            <div class="rass">
            <?php
            if (!empty($followers)) {
                foreach ($followers as $user) {
                    echo '<div class="result-item">';
                    echo '<img src="' . htmlspecialchars($user['photo_profil']) . '" alt="Profil" class="result-avatar">';
                    echo '<div class="result-content">';
                    echo '<h3>' . htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']) . '</h3>';
                    echo '<p>' . htmlspecialchars($user['bio']) . '</p>';
                    echo '</div>';
                    $buttonText = $user['isMutual'] > 0 ? "Se désabonner" : "S'abonner";
                    echo '<button class="subscribe-button" data-userid="' . htmlspecialchars($user['id_utilisateur']) . '">' . $buttonText . '</button>';
                    echo '</div>';
                }
            } else {
                echo '<p>Pas de suggestions disponibles.</p>';
            }
            ?>
            </div>
        </div>
</div>
<?php include "header_end.php"; ?>