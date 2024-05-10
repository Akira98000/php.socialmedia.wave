<?php
require_once "../model/function.php";
setlocale(LC_TIME, 'fr_FR.UTF-8', 'French_France.1252');
verifierConnexion();
$userId = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : '';

if ($userId) {
    $infosUtilisateur = chargerInfosUtilisateur($bdd, $userId);
    if ($infosUtilisateur) {
        extract($infosUtilisateur);
        $nonFriends = $infosUtilisateur['nonFriends']; 
        $friendsList = getFriendsList($bdd, $userId);
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
    <h2><?php 
        $formatNomPrenom = '@' . strtolower($prenom . '_' . $nom);
        echo ' ' . $formatNomPrenom;
        
        ?></h2>
    </div>      
    <div class="profile-list">
    <a href="main.php" class="retour-btn">Retour</a>
    <?php
        $compteur = 0;
        if (isset($_SESSION['id_utilisateur'])) {
            $userId = $_SESSION['id_utilisateur'];
            $friendsList = getFriendsList($bdd, $userId);

            foreach ($friendsList as $friendId) {
                if ($compteur >= 5) break;
                $friendInfo = getInfo($bdd, $friendId);
                $friendName = htmlspecialchars($friendInfo['prenom']) . ' ' . htmlspecialchars($friendInfo['nom']);
                $profilePicture = !empty($friendInfo['photo_profil']) ? $friendInfo['photo_profil'] : 'default.png';
                echo "<a href='?ami={$friendInfo['id_utilisateur']}' class='profile-link'>";
                echo "<img src='{$profilePicture}' alt='Profil de {$friendName}' class='user-photo' title='{$friendName}'>";
                echo "<div class='user-info'>";
                echo "<div class='user-name'>{$friendName}</div>";
                echo "</div>";  
                echo "</a>";
                $compteur++;
            }
        }
        ?>
</div>

    <div class="chat">
    <?php 
    $amiActuelInfo = null;
    if (isset($_GET['ami'])): 
        $amiActuelId = $_GET['ami'];
        $amiActuelInfo = getInfo($bdd, $amiActuelId);
        if ($amiActuelInfo): ?>
            <div class="amis-details">
                <?php if (!empty($amiActuelInfo['photo_profil'])): ?>
                    <img src="<?php echo htmlspecialchars($amiActuelInfo['photo_profil']); ?>" alt="Image de profil">
                <?php endif; ?>
                <div class="nom"><?= htmlspecialchars($amiActuelInfo['prenom']) . " " . htmlspecialchars($amiActuelInfo['nom']) ?></div>
            </div>

            <div class="messages">
                <?php
                $messages = getMessages($bdd, $userId, $amiActuelId);
                if (empty($messages)) {
                    echo "<div class='no-messages'>Vous n'avez pas échangé de message encore.</div>";
                } else {
                    $groupedMessages = groupMessagesByDate($messages);
                    foreach ($groupedMessages as $date => $msgs) {
                        $formattedDate = strftime('%d %B %Y', strtotime($date));
                        echo "<div class='date-separator'>$formattedDate</div>"; 
                        foreach ($msgs as $msg) {
                            $messageClass = ($msg['sender_id'] == $userId) ? 'message-envoye' : 'message-recu';
                            echo "<div class='message $messageClass'>";
                            echo "<div class='message-text'>" . htmlspecialchars($msg['message']) . "</div>";
                            echo "</div>";
                        }
                    }
                }
                ?>
            </div>
            
            <div class="envoyer-message">
                <form action="../controler/envoyer_message.php" method="post">
                    <input type="hidden" name="receiver_id" value="<?= $amiActuelId ?>">
                    <div class="input-wrapper"> 
                        <textarea name="message" placeholder="Votre message..." required></textarea>
                        <button type="submit">Envoyer</button>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <div class='no-messages'>Sélectionnez une conversation pour commencer à échanger des messages.</div>
        <?php endif; ?>
    <?php else: ?>
        <div class='no-messages'>Sélectionnez une conversation pour commencer à échanger des messages.</div>
    <?php endif; ?>
</div>
</div>
<script>
        function loadMessages() {
            const amiId = '<?php echo addslashes($amiActuelId); ?>';
            $.ajax({
                url: '../controler/recuperer_message.php?ami=' + amiId,
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    const messagesContainer = $('.messages');
                    messagesContainer.empty();

                    if (data.length === 0) {
                    messagesContainer.append("<div class='no-messages'>Vous n'avez pas échangé de message encore.</div>");
                    } else {
                    let currentDate = '';
                    data.forEach(function (message) {
                        if (message.date !== currentDate) {
                            currentDate = message.date;
                            messagesContainer.append(`<div class='date-separator'>${currentDate}</div>`);
                        }

                        const messageClass = message.sender_id == amiId ? 'message-recu' : 'message-envoye';
                        const messageElement = `
                            <div class="message ${messageClass}">
                                <div class="message-text">${message.text}</div>
                                <div class="message-time">${message.time}</div>
                            </div>
                        `;
                        messagesContainer.append(messageElement);
                    });
                }}
            });
        }




        document.addEventListener('DOMContentLoaded', (event) => {
            loadMessages();

            document.querySelector('.envoyer-message form').addEventListener('submit', function(e) {
                e.preventDefault(); 

                const form = this;
                const data = new FormData(form);

                fetch('../controler/envoyer_message.php', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        form.querySelector('textarea').value = '';
                        loadMessages(); 
                    } else {
                        alert(data.error);
                    }
                })
                .catch((error) => {
                    console.error('Erreur lors de l\'envoi du message:', error);
                });
            });
        });
</script>
<?php include "header_end.php"; ?>