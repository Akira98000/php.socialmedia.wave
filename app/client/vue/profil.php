<?php
require_once "../model/function.php";

verifierConnexion();
$userId = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : '';

if ($userId) {
    $infosUtilisateur = chargerInfosUtilisateur($bdd, $userId);
    if ($infosUtilisateur) {
        extract($infosUtilisateur);
        $nonFriends = $infosUtilisateur['nonFriends']; 
        // $publicationsSuivies = $infosUtilisateur['publicationsSuivies']; 
        $publicationsEnregistrees =  $infosUtilisateur['enregistrer'];
    }
}
?>  
<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wave &copy;</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/> <!-- IMPORT POUR LES ICONS-->
</head>
<body>
    <!-- <div id="loadingScreen" class="loading-screen">
        <img src="images/icon_wave.png" alt="Logo" class="logo">
        <div class="loader"></div>
    </div> -->
    <div class="feed">
        <div class="feed__header">
            <h2>Mon profil</h2>
        </div>
        <div class="profile">
            <div class="profile__topBackground"></div>
            <div class="profile__info">
                <div class="profile__avatar">
                    <img src="<?php echo htmlspecialchars($infosUtilisateur['photo_profil']); ?>" alt="Image de profil">
                    <!-- <img src="uploads/default.png" alt=""> -->
                </div>
                <div class="profile__details">
                    <h3><?php echo htmlspecialchars($nom . ' ' . $prenom); ?> 
                        <span class="post__headerSpecial">
                            <span class="material-icons post__badge"></span>
                        </span>
                    </h3>
                    <p><?php echo htmlspecialchars($pays .' ' .$ville); ?></p>
                    <p>née le <?php echo htmlspecialchars($datenaissance); ?></p>
                    <div class="profile__description">
                        <p><?php echo htmlspecialchars($bio); ?></p>
                    </div>
                </div>
                <div class="profile__stats">
                    <a href="profil.php">
                    <div class="profile__stat">
                        <span>Publication</span>
                        <span><?php echo $totalPosts; ?></span>
                    </div>
                    </a>
                    <a href="mesfollowers.php">
                    <div class="profile__stat">
                        <span>Followers</span>
                        <span><?php echo $follower; ?></span>
                    </div>
                    </a>
                    <a href="mesfollowing.php">
                    <div class="profile__stat">
                        <span>Following</span>
                        <span><?php echo $suivi; ?></span>
                    </div>
                    </a>
                </div>
            </div>
        </div>
        <?php
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
              if ($isLiked) {
                  $likeClass .= ' liked';
              }
              if ($isLiked) {
                $likeClass .= ' liked';
              }
              if($isSave){
                $saveClass .= ' saved';
               }
            
              echo '<div class="post">';
              echo '<div class="post__avatar">';
              echo '<img src="' . htmlspecialchars($infosUtilisateur['photo_profil']) .'" alt="publication"/>';
              echo '</div>';
              echo '<div class="post__body">';
              echo '<div class="post__header">';
              echo '<div class="post__headerText">';
              echo '<h3>' . htmlspecialchars($nom . ' ' . $prenom) .' <span class="post__headerSpecial"> ';
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
              echo '</div>';
              echo '<div class="post__avatar">';
              echo '</div>';
              echo '</div>';
              echo '<div class="post">';
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