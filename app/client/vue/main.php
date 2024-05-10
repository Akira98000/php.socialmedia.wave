<?php
require_once "../model/function.php";

verifierConnexion();
$userId = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : '';

if ($userId) {
    $publicationsParPage = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $debut = ($page) * $publicationsParPage;
    $infosUtilisateur = chargerInfosUtilisateur($bdd, $userId, $debut, $publicationsParPage);
    if ($infosUtilisateur) {
        extract($infosUtilisateur);
        $nonFriends = $infosUtilisateur['nonFriends']; 
        // $publicationsSuivies = $infosUtilisateur['publicationsSuivies']; 
        $publicationsSuivies = getFollowingPublications($bdd, $userId, $page, $publicationsParPage);
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
    <?php
    if (empty($publicationsSuivies)) {
      echo '<div class="conteneur-centre">';
      echo '<p "message-centre" >Aucune publication sur votre mur Waves, <br> vous devez d\'abord vous abonner à des personnes <br>pour suivre les waves.</p>';
      echo '</div>';
    }
    else {
      foreach ($publicationsSuivies as $publication) {
          $likesCount = getLikesCountForPublication($bdd, $publication['id']);
          $isLiked = isPublicationLiked($bdd, $publication['id'], $userId);
          $isSave = estPublicationEnregistree($bdd, $userId,$publication['id']);
          $description = $publication['texte_article']; 
          $likeClass = 'like-button';
          $saveClass = 'save-button';
          $dislikeCount = getDislikesCountForPublication($bdd, $publication['id']);
          $isDisliked = isPublicationDisliked($bdd, $publication['id'], $userId);
          $dislikeClass = 'dislike-button';
          if ($isDisliked) {
            $dislikeClass .= ' disliked';
          }
        
          if ($isLiked) {
              $likeClass .= ' liked';
          }
          if($isSave){
            $saveClass .= ' saved';
          }

          $nom = $publication['nom'];
          $prenom = $publication['prenom'];
          $nom_format = '@' . strtolower(str_replace(' ', '_', $prenom . '_' . $nom));
          $nom_format_id = htmlspecialchars($nom_format);
          echo '<div class="post">';
          echo '<div class="post__avatar">';
          echo '<img src="' . htmlspecialchars($publication['photo_profil']) .'" alt="publication"/>';
          echo '</div>';
          echo '<div class="post__body">';
          echo '<div class="post__header">';
          echo '<div class="post__headerText">';
          echo '<h3>' . htmlspecialchars($publication['nom'] . ' ' . $publication['prenom']) .' <span class="post__headerSpecial"> ';
          echo ' <span class="material-icons post__badge"></span> '. $nom_format_id .'</span></h3>';
          echo '</div>';
          echo '<div class="post__headerDescription">';
          echo '<p class="description">' . $description . '</p>';
          echo '</div>';
          echo '</div>';
          echo '<a href="postID.php?id=' . htmlspecialchars($publication['id']) . '" class="post-link">';
          $image_article = htmlspecialchars($publication['image_article']);
            if (pathinfo($image_article, PATHINFO_EXTENSION) === 'mp4') {
                echo '<video controls>';
                echo '<source src="' . $image_article . '" type="video/mp4">';
                echo 'Your browser does not support the video tag.';
                echo '</video>';
            } else {
                echo '<img src="' . $image_article . '" alt=""/>';
            }
          echo '</a>';
          echo '<div class="post__footer">';
          echo '<a class="'. $likeClass .'" data-postid="' . $publication['id'] . '" data-userid="' . $userId . '">';
          echo '<span class="material-icons">thumb_up</span>';
        //   echo '<span class="like-count">' . $likesCount . '</span>';
          echo '</a>';
          echo '<a class="'. $dislikeClass .'" data-postid="' . $publication['id'] . '" data-userid="' . $userId . '">';
            echo '<span class="material-icons">thumb_down</span>';
            // echo '<span class="dislike-count" style="margin-left: 5px;">' . $dislikeCount . '</span>';
            echo '</a>';
          echo '<a class="comment-button" data-postid="' . $publication['id'] . '">';
          echo '<span class="material-icons">comment</span>';
          echo '</a>';
          echo '<a class="'. $saveClass .'" data-postid="' . $publication['id'] . '" data-userid="' . $userId . '">';
          echo '<span class="material-icons">bookmark</span>';
          echo '</a>';  
          echo '</div>';
          echo '<div class="reply-section" id="reply-section-' . $publication['id'] . '">';
            echo '<div class="reply-to">Repondre à '. htmlspecialchars($publication['nom'] . ' ' . $publication['prenom']).'</div>';
           echo '<textarea class="reply-input" id="reply-input-' . $publication['id'] . '" placeholder="Commenter la publication ... "></textarea>';
             echo '<div class="reply-actions">';
              echo '<button class="reply-button" data-postid="' . $publication['id'] . '">Envoyer</button>';
              echo '</div>';
            echo '</div>';
          echo '</div>';
          echo '<div class="post__avatar">';
          echo '</div>';
          echo '</div>';
          echo '<div class="post">';
          echo '</div>';           
      }
      
      $nombreTotalPages = getNombreTotalPages($bdd, $userId, $publicationsParPage);
      echo '<div class="pagination-container">';
        for ($i = 1; $i <= $nombreTotalPages; $i++) {
            echo "<a class='page-link' href='?page=$i'>$i</a> ";
        }
      echo '</div>';
    }
  ?>        
</div>
<script src="js/save_btn.js"></script>
<script src="js/like.js"></script>
<script src="js/comment_btn.js"></script>
<script>
    document.querySelectorAll('.reply-button').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault(); 

        var postId = this.getAttribute('data-postid');
        var commentInput = document.querySelector('#reply-input-' + postId);
        var commentaire = commentInput.value.trim();

        if (commentaire === '') {
            alert('Entrer commentaire.');
            return;
        }

        var formData = new FormData();
        formData.append('publicationId', postId);
        formData.append('comment', commentaire);

        fetch('../controler/comment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                commentInput.value = ''; 
                alert('Commentaire poster avec succès');
            } else {
                alert('Commentaire non poster');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
</script>
<?php include "header_end.php"; ?>