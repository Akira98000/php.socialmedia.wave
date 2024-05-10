<?php
include '../model/function.php';

if(isset($_POST['query'])) {
    $texte = $_POST['query'];
    $resultats = rechercherUtilisateurs($texte, $bdd);
    
    if(count($resultats) > 0) {
        foreach ($resultats as $utilisateur) {
            echo '<div class="search-results">';
            echo '    <a href="searchR.php?id=' . htmlspecialchars($utilisateur['id_utilisateur']) . '" class="search-result-item">';
            echo '        <div class="result-item">';
            echo '            <img src="' . htmlspecialchars($utilisateur['photo_profil']) . '" alt="Profil" class="result-avatar">';
            echo '            <div class="result-content">';
            echo '                <h3>' . htmlspecialchars($utilisateur['nom']) . ' ' . htmlspecialchars($utilisateur['prenom']) . '</h3>';
            echo '                <p>' . htmlspecialchars($utilisateur['bio']) . '</p>'; 
            echo '            </div>';
            echo '        </div>';
            echo '    </a>';
            echo '</div>';
        }
    } else {
        echo "Aucun résultat trouvé.";
    }
}
?>
