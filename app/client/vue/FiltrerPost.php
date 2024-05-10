<?php
require_once "../model/function.php";

verifierConnexion();
$userId = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : '';

if ($userId) {
    $publicationsParPage = 5;
    $dateDebut = '2024-01-24';
    $dateFin = '2024-01-28';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $debut = ($page) * $publicationsParPage;
    $infosUtilisateur = chargerInfosUtilisateur($bdd, $userId, $debut, $publicationsParPage);
    if ($infosUtilisateur) {
        extract($infosUtilisateur);
        $nonFriends = $infosUtilisateur['nonFriends']; 
        $publicationsSuivies = FiltrerFollowingPublications($bdd, $userId, $page, $publicationsParPage, $dateDebut, $dateFin);
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
    <div class="titleContainer">
        <div class="search-header">
                <h2>Filtrer publication</h2>
        </div>
    </div>
    <div class="formFiltrer">
        <form id="formFiltrerDates">
            <label for="dateDebut">Date de début :</label>
            <input type="date" id="dateDebut" name="dateDebut" required>
            <label for="dateFin">Date de fin :</label>
            <input type="date" id="dateFin" name="dateFin" required>
            <button type="submit">Filtrer</button>
        </form>
    </div>
    <div id="resultatsPublications">
    </div>
</div>

<script src="js/save_btn.js"></script>
<script src="js/like.js"></script>
<script src="js/comment_btn.js"></script>
<script>
$(document).ready(function() {
    $('#formFiltrerDates').on('submit', function(e) {
        e.preventDefault();

        var dateDebut = $('#dateDebut').val();
        var dateFin = $('#dateFin').val();

        $.ajax({
            type: "POST",
            url: "../controler/Filtrer.php", 
            data: {
                dateDebut: dateDebut,
                dateFin: dateFin,
                userId: <?php echo json_encode($userId); ?>,
                page: 1, 
                publicationsParPage: 10 
            },
            success: function(response) {
                $('#resultatsPublications').html(response);
            },
            error: function() {
                alert("Erreur lors de l'envoi des données.");
            }
        });
    });
});
</script>
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