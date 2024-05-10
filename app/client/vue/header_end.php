<?php
require_once "../model/function.php";

verifierConnexion();
$userId = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : '';

if ($userId) {
    $infosUtilisateur = chargerInfosUtilisateur($bdd, $userId);
    if ($infosUtilisateur) {
        extract($infosUtilisateur);
        $nonFriends = $infosUtilisateur['nonFriends']; 
        $notifications = getNotificationsUtilisateur($bdd, $userId);
    }
}

?>
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

<div class="widgets">

    <?php 
    $currentFileName = basename($_SERVER['PHP_SELF']);
    if ($currentFileName !== 'notification.php'): 
    ?>
    <div class="widgets__widgetContainer">
        <h2>Mes notifications</h2>
        <div class="friend">
            <ul class="notification-list">
            <?php
            if (empty($notifications)) {
                echo '<p>Il n\'y a pas de notification pour l\'instant.</p>';
            } else {
                echo '<ul class="notification-list">';
                $count = 0;
                foreach ($notifications as $notification) {
                    if ($count >= 3) {
                        break;
                    }
                    echo '<div class="notification">';
                    echo '<img src="' . htmlspecialchars($notification['photo_profil']) . '" alt="Avatar" class="user-avatar">';
                    echo '<div class="notification-content">';
                    echo '<p class="notification-text">' . htmlspecialchars($notification['contenu']) .' '. htmlspecialchars($notification['prenom']) .'</p>';
                    echo '<p class="notification-time">Date: ' . htmlspecialchars($notification['date_notification']) . '</p>';
                    echo '</div>';
                    echo '<div class="notification-user">';
                    echo '</div>';
                    echo '</div>';
                    $count++; 
                }
                echo '</ul>';
            }
            ?>
            </ul>
        </div>
    </div>
    <?php
        endif;
    ?>
    <?php
        $currentFileName = basename($_SERVER['PHP_SELF']);
        if ($currentFileName != 'amis.php'):
        ?>
            <div class="widgets__widgetContainer">
                <h2>Suggestions d'amis</h2>
                <div class="friend">
                    <?php if (empty($nonFriends)): ?>
                        <p>Vous n'avez plus de suggestion d'amis.</p>
                    <?php else: ?>
                        <ul class="friend-suggestions">
                            <?php $count = 0; ?>
                            <?php foreach ($nonFriends as $user): ?>
                                <?php if ($count >= 3) {
                                    break; 
                                } ?>
                                <li class="friend-suggestion">
                                    <img src="<?php echo htmlspecialchars($user['photo_profil']); ?>" alt="Profil" class="friend-avatar">
                                    <div class="friend-info">
                                        <p class="friend-name"><?php echo htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']); ?></p>
                                    </div>
                                    <button class="subscribe-button" data-userid="<?php echo htmlspecialchars($user['id_utilisateur']); ?>">S'abonner</button>
                                </li>
                                <?php $count++; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php
        endif;
        ?>
</div>
    <script>
    window.addEventListener('load', function() {
        document.getElementById('loadingScreen').style.display = 'none';
    });
    </script>
    <script src="js/loading.js"></script>
</body>
</html>