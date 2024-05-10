<?php
require_once '../model/function.php'; 

if (isset($_POST['dateDebut'], $_POST['dateFin'], $_POST['userId'])) {
    $userId = $_POST['userId'];
    $dateDebut = $_POST['dateDebut'];
    $dateFin = $_POST['dateFin'];
    $page = $_POST['page'] ?? 1; 
    $publicationsParPage = $_POST['publicationsParPage'] ?? 10; 
    $publications = FiltrerFollowingPublications($bdd, $userId, $page, $publicationsParPage, $dateDebut, $dateFin);
    if (!empty($publications)) {
        foreach ($publications as $publication) {
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
    } else {
        echo "<p>Aucune publication trouvée pour cette période.</p>";
    }
} else {
    echo "<p>Données requises non fournies.</p>";
}
?>
