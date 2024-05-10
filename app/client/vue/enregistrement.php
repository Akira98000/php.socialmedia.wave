<?php
require_once "../model/function.php";

verifierConnexion();
$userId = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : '';

if ($userId) {
    $publicationsParPage = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $debut = ($page - 1) * $publicationsParPage;
    $infosUtilisateur = chargerInfosUtilisateur($bdd, $userId, $debut, $publicationsParPage);
    if ($infosUtilisateur) {
        extract($infosUtilisateur);
        $nonFriends = $infosUtilisateur['nonFriends']; 
        $publicationsEnregistrees =  $infosUtilisateur['enregistrer'];
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
    <div class="search-container">
        <div class="search-header">
            <h2>Mes publications enregistrées</h2>
        </div>
    <?php
    foreach ($publicationsEnregistrees as $publication) {
        $likesCount = getLikesCountForPublication($bdd, $publication['id']);
        $dislikeCount = getDisLikesCountForPublication($bdd, $publication['id']);
        $isLiked = isPublicationLiked($bdd, $publication['id'], $userId);
        $isSave = estPublicationEnregistree($bdd, $userId,$publication['id']);
        $description = $publication['texte_article']; 
        $likeClass = 'like-button';
        $dislikeClass = 'dislike-button';
        $saveClass = 'save-button';

        if ($isLiked) {
            $likeClass .= ' liked';
        }
        if($isSave){
          $saveClass .= ' saved';
        }

        echo '<div class="post">';
        echo '<div class="post__avatar">';
        echo '</div>';
        echo '<div class="post__body">';
        echo '<div class="post__header">';
        echo '<div class="post__headerText">';
        echo '</div>';
        echo '<div class="post__headerDescription">';
        echo '<p class="description">' . $description . '</p>';
        echo '</div>';
        echo '</div>';
        $image_article = htmlspecialchars($publication['image_article']);
        if (pathinfo($image_article, PATHINFO_EXTENSION) === 'mp4') {
            echo '<video controls>';
            echo '<source src="' . $image_article . '" type="video/mp4">';
            echo 'Your browser does not support the video tag.';
            echo '</video>';
        } else {
            echo '<img src="' . $image_article . '" alt=""/>';
        }
        echo '<div class="post__footer">';
        echo '</div>';
        echo '</div>';
        echo '<div class="post__avatar">';
        echo '</div>';
        echo '</div>';
        echo '<div class="post">';
        echo '</div>';           
    }
    ?>        
</div>
</div>
</div>
<script>
document.querySelectorAll('.like-button').forEach(button => {
  button.addEventListener('click', function(event) {
    event.preventDefault(); 
    var postId = this.getAttribute('data-postid');
    var userId = this.getAttribute('data-userid');

    fetch('../controler/like.php', {
      method: 'POST',
      body: JSON.stringify({ post_id: postId, user_id: userId }),
      headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        this.classList.toggle('liked');
        var likeCounter = this.querySelector('.like-count');
        if (likeCounter) {
          likeCounter.textContent = data.new_like_count;
        }
      }
    })
    .catch(error => {
      console.error('Erreur lors de la requête AJAX:', error);
    });
  });
});
</script>
<script>
document.querySelectorAll('.dislike-button').forEach(button => {
  button.addEventListener('click', function(event) {
    event.preventDefault(); 
    var postId = this.getAttribute('data-postid');
    var userId = this.getAttribute('data-userid');

    fetch('../controler/dislike.php', {
      method: 'POST',
      body: JSON.stringify({ post_id: postId, user_id: userId }),
      headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        this.classList.toggle('liked');
        var likeCounter = this.querySelector('.dislike-count');
        if (likeCounter) {
          likeCounter.textContent = data.new_like_count;
        }
      }
    })
    .catch(error => {
      console.error('Erreur lors de la requête AJAX:', error);
    });
  });
});
</script>
<script>
document.querySelectorAll('.save-button').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        var postId = this.getAttribute('data-postid');
        var userId = this.getAttribute('data-userid');

        fetch('../controler/enregistrer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'userId=' + userId + '&postId=' + postId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Publication enregistrée');
                this.classList.toggle('saved');
            } else {
                console.log(data.message ? data.message : 'Erreur');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    });
});
</script>
<?php include "header_end.php"; ?>