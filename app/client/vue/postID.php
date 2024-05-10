<?php
require_once "../model/function.php";

verifierConnexion(); // Assurez-vous que cette fonction initie bien la session et vérifie l'utilisateur

$publicationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($publicationId > 0) {
    $detailsPublication = getPublicationDetails($bdd, $publicationId);
    if (!$detailsPublication) {
        echo "Publication non trouvée.";
        exit;
    }
    $comments = getCommentsForPublication($bdd, $publicationId);
    $likesCount = getLikesCountForPublication($bdd, $publicationId);
    $dislikesCount = getDislikesCountForPublication($bdd, $publicationId);
    $isLiked = isPublicationLiked($bdd, $publicationId, $_SESSION['id_utilisateur']);
    $isDisliked = isPublicationDisliked($bdd, $publicationId, $_SESSION['id_utilisateur']);
    $isSaved = estPublicationEnregistree($bdd, $_SESSION['id_utilisateur'], $publicationId);
    $likeClass = $isLiked ? 'like-button liked' : 'like-button';
    $dislikeClass = $isDisliked ? 'dislike-button disliked' : 'dislike-button';
} else {
    echo "Requête invalide.";
    exit;
}
?>
<?php include "header.php"; ?>
<div class="feed">
    <div class="feed__header">
        <h2>Détails de la publication</h2>
    </div>
    <div class="post">
        <div class="post__avatar">
            <img src="<?php echo htmlspecialchars($detailsPublication['photo_profil']); ?>" alt="Avatar de l'utilisateur"/>
        </div>
        <div class="post__body">
            <div class="post__header">
                <div class="post__headerText">
                    <h3><?php echo htmlspecialchars($detailsPublication['prenom'] . ' ' . $detailsPublication['nom']); ?></h3>
                </div>
                <div class="post__headerDescription">
                    <p class="description"><?php echo htmlspecialchars($detailsPublication['texte_article']); ?></p>
                </div>
            </div>
            <?php
                 $image_article = htmlspecialchars($detailsPublication['image_article']);
                 if (pathinfo($image_article, PATHINFO_EXTENSION) === 'mp4') {
                     echo '<video controls>';
                     echo '<source src="' . $image_article . '" type="video/mp4">';
                     echo 'Your browser does not support the video tag.';
                     echo '</video>';
                 } else {
                     echo '<img src="' . $image_article . '" alt="" alt="Image de la publication" style="max-width: 100%;"/>';
                 }
            ?>
        <div class="post__footer">
            <a href="#" class="<?php echo $likeClass; ?>" data-postid="<?php echo $publicationId; ?>" data-userid="<?php echo $_SESSION['id_utilisateur']; ?>">
                <span class="material-icons">thumb_up</span>
                <span class="like-count"><?php echo $likesCount; ?></span>
            </a>
            <a href="#" class="<?php echo $dislikeClass; ?>" data-postid="<?php echo $publicationId; ?>" data-userid="<?php echo $_SESSION['id_utilisateur']; ?>">
                <span class="material-icons">thumb_down</span>
                <span class="dislike-count"><?php echo $dislikesCount; ?></span>
            </a>
        </div>
        <div class="comments-section">
            <?php if (!empty($comments)): ?>
                <h3 class="text3">Les Commentaires du post</h3>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment__header">
                            <strong><?php echo htmlspecialchars($comment['prenom'] . ' ' . $comment['nom']); ?></strong>
                        </div>
                        <div class="comment__body">
                            <?php echo htmlspecialchars($comment['comment']); ?>
                        </div>
                        <div class="comment__footer">
                            <small>Posté le <?php echo date('d/m/Y à H:i', strtotime($comment['created_at'])); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun commentaire pour cette publication.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="post__avatar">
    </div>
</div>
</div>
<script src="js/save_btn.js"></script>
<script src="js/like.js"></script>
<script src="js/comment_btn.js"></script>
<?php include "header_end.php"; ?>
