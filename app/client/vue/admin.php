<?php
require_once "../model/function.php";

verifierConnexion();
$userId = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : '';

if ($userId) {
    $infosUtilisateur = chargerInfosUtilisateur($bdd, $userId);
    if ($infosUtilisateur) {
        $nom = $infosUtilisateur['nom'];
        $prenom = $infosUtilisateur['prenom'];
        $bio = $infosUtilisateur['bio'];
        $nonFriends = $infosUtilisateur['nonFriends']; 
        $usersBlocked = getBlockedUsers($bdd);
    }
}

?>  
<?php include "header.php"; ?>

<div class="feed">
    <div class="feed__header">
        <h2><?php echo '@admin_' . strtolower($prenom . '_' . $nom); ?></h2>
    </div>     
    <div class="search-container">
        <div class="search-header">
            <h2>Envoyer une notification à tout le monde</h2>
        </div>
        <div class="message-input">
            <span class="material-icons">search</span>
            <input type="text"  id="messageInput"  placeholder="Saisir un message" />
            <button id="sendButton">Envoyer</button>
        </div>
        <div class="search-header">
            <h2>Utilisateur bloqués</h2>
        </div>
        <div>
            <?php
            foreach ($usersBlocked as $user) {
                echo '<div class="search-results">';
                echo '        <div class="result-item">';
                echo '            <img src="' . htmlspecialchars($user['photo_profil']) . '" alt="Profil" class="result-avatar">';
                echo '            <div class="result-content">';
                echo '                <h3>' . htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']) . '</h3>';
                echo '                <p>' . htmlspecialchars($user['bio']) . '</p>'; 
                echo '            </div>';
                echo '        </div>';
                echo '</div>';
            }
            ?>
        </div>
        <div class="search-header">
            <h2>Rechercher un utilisateur</h2>
        </div>
        <div class="search-input">
            <span class="material-icons">search</span>
            <input type="text" id="searchInput" placeholder="Recherche par nom ou prénom" />
        </div>
        <div class="rass">
            <div id="resultats">
            
            </div>
        </div>
    </div>
</div>

<?php include "header_end.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById('sendButton').addEventListener('click', function() {
    var userMessage = document.getElementById('messageInput').value;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../controler/envoieNotifAll.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            console.log(this.responseText);
            alert("Message envoyé avec succès !");
        }
    };
    xhr.send('message=' + encodeURIComponent(userMessage));
    });

    $(document).ready(function(){
        $("#searchInput").keyup(function(){
            var text = $(this).val();
            $.ajax({
                url: '../controler/SearchAdminHandler.php', 
                method: 'POST',
                data: {query: text},
                success: function(response){
                    $("#resultats").html(response);
                }
            });
        });

        $(document).on('click', '.delete-user-btn', function() {
        const $button = $(this); 
        const userId = $button.data('user-id');
        if(confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
            $.ajax({
                url: '../controler/deleteUser.php',
                type: 'POST',
                data: { user_id: userId },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        console.log('Utilisateur supprimé', response.message);
                        $button.closest('.result-item').remove(); 
                    } else {
                        console.error('Une erreur est survenue', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Une erreur est survenue lors de la communication avec le serveur.', xhr.responseText);
                }
            });
        }
        });

        $(document).on('click', '.block-user-btn', function() {
        const $button = $(this); 
        const userId = $button.data('user-id');
        if(confirm('Êtes-vous sûr de vouloir bloquer cet utilisateur ?')) {
            $.ajax({
                url: '../controler/blockUser.php',
                type: 'POST',
                data: { user_id: userId },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        console.log('Utilisateur bloquer', response.message);
                        $button.closest('.result-item').remove(); 
                    } else {
                        console.error('Une erreur est survenue', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Une erreur est survenue lors de la communication avec le serveur.', xhr.responseText);
                }
            });
        }
    });

    });
</script>