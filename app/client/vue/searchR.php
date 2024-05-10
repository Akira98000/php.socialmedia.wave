<?php
require_once "../model/function.php";

verifierConnexion();
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = $_GET['id'];
    $profileInfo = getProfileInfo($bdd, $userId);
    $informationG = getInfo($bdd,$userId);
    $estadmin = $_SESSION['est_admin'];
}


?>  
<?php include "header.php"; ?>
    <!-- <div id="loadingScreen" class="loading-screen">
        <img src="images/icon_wave.png" alt="Logo" class="logo">
        <div class="loader"></div>
    </div> -->
    <div class="feed">
        <div class="feed__header">
            <h2>Profil de <?php echo htmlspecialchars($profileInfo['nom']) . ' ' . htmlspecialchars($profileInfo['prenom']); ?></h2>
        </div>
        <div class="profile">
            <div class="profile__topBackground"></div>
            <div class="profile__info">
                <div class="profile__avatar">
                    <img src="<?php echo htmlspecialchars($profileInfo['photo_profil']); ?>" alt="Image de profil">
                    <!-- <img src="uploads/default.png" alt=""> -->
                </div>
                <div class="profile__details">
                    <h3><?php echo htmlspecialchars($profileInfo['nom']) . ' ' . htmlspecialchars($profileInfo['prenom']); ?> 
                        <span class="post__headerSpecial">
                            <span class="material-icons post__badge"></span>
                        </span>
                    </h3>
                    <p><?php echo htmlspecialchars($profileInfo['ville']) . ' ' . htmlspecialchars($profileInfo['pays']); ?></p>
                    <p>née le <?php htmlspecialchars($profileInfo['date_de_naissance']); ?></p>
                    <div class="profile__description">
                        <p><?php echo htmlspecialchars($profileInfo['bio']); ?></p>
                    </div>
                </div>
                <div class="profile__stats">
                    <a href="profil.php">
                    <div class="profile__stat">
                        <span>Publication</span>
                        <span><?php echo '<p>' . htmlspecialchars($profileInfo['postCount']) . '</p>'; ?></span>
                    </div>
                    </a>
                    <div class="profile__stat">
                        <span>Followers</span>
                        <span><?php echo '<p>' . htmlspecialchars($profileInfo['followersCount']) . '</p>'; ?></span>
                    </div>
                    <div class="profile__stat">
                        <span>Following</span>
                        <span><?php  echo '<p>' . htmlspecialchars($profileInfo['followingsCount']) . '</p>'; ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
          foreach ($profileInfo['publications'] as $publication) {
              $likesCount = getLikesCountForPublication($bdd, $publication['id']);
              $isLiked = isPublicationLiked($bdd, $publication['id'], $userId);
              $likeClass = $isLiked ? 'liked' : '';
              $description = $publication['texte_article']; 
              $likeClass = 'like-button';
              if ($isLiked) {
                  $likeClass .= ' liked';
              }
              echo '<div class="post">';
              echo '<div class="post__avatar">';
              echo '<img src="' . htmlspecialchars($profileInfo['photo_profil']).'" alt="publication"/>';
              echo '</div>';
              echo '<div class="post__body">';
              echo '<div class="post__header">';
              echo '<div class="post__headerText">';
              echo '<h3>' . htmlspecialchars($profileInfo['nom']) . ' ' . htmlspecialchars($profileInfo['prenom']) .' <span class="post__headerSpecial"> ';
              echo ' <span class="material-icons post__badge"> verified </span></span></h3>';
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
              echo '<a class="'. $likeClass .'" data-postid="' . $publication['id'] . '" data-userid="' . $userId . '">';
              echo '<span class="material-icons">thumb_up</span>';
              echo '<span class="like-count">  ' . $likesCount . '  </span>';
              echo '</a>';
              echo '<a href="" class="dislike-button"><span class="material-icons">thumb_down</span></a>';
              echo '<a class="comment-button" data-postid="' . $publication['id'] . '">';
              echo '<span class="material-icons">comment</span>';
              echo '</a>';
              if ($estadmin == 1) {
                echo '<button class="delete-button" data-postid="' . $publication['id'] . '">Supprimer</button>';
            }
            
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
<script>

document.addEventListener('DOMContentLoaded', function() {
    var deleteButtons = document.querySelectorAll('.delete-button');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce post?')) {
                var postId = this.getAttribute('data-postid');
                
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../controler/deletePost.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                
                xhr.onload = function() {
    if (this.status === 200) {
        console.log('Réponse du serveur :', this.responseText);
        var response = JSON.parse(this.responseText);
        if (response.success) {
            console.log('Le post a été supprimé avec succès.');
        } else {
            console.error('Erreur lors de la suppression du post :', response.message);
        }
    } else {
        console.error('Erreur lors de la requête :', this.status);
    }
};
                
                xhr.send('post_id=' + postId);
            }
        });
    });
});

document.querySelectorAll('.like-button').forEach(button => {
  button.addEventListener('click', function(event) {
    event.preventDefault(); 
    var postId = this.getAttribute('data-postid');
    var userId = this.getAttribute('data-userid');
    fetch('../model/like.php', {
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
    <?php include "header_end.php"; ?>