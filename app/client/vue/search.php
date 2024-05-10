<?php
require_once "../model/function.php";

verifierConnexion();
$userId = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : '';

if ($userId) {
    $infosUtilisateur = chargerInfosUtilisateur($bdd, $userId);
    if ($infosUtilisateur) {
        extract($infosUtilisateur);
        $nonFriends = $infosUtilisateur['nonFriends']; 
    }
}

?>  
<?php include "header.php"; ?>
<script>
        $(document).ready(function(){
            $("#searchInput").keyup(function(){
                var text = $(this).val();
                $.ajax({
                    url: '../controler/searchHandler.php',
                    method: 'POST',
                    data: {query: text},
                    success: function(response){
                        $("#resultats").html(response);
                    }
                });
            });
        });
    </script>
<div class="feed">
    <div class="feed__header">
        <h2><?php $formatNomPrenom = '@' . strtolower($prenom . '_' . $nom);
        echo ' '.$formatNomPrenom;?></h2>
    </div>      
    <div class="search-container">
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