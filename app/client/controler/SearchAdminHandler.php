<?php
include '../model/function.php';

if(isset($_POST['query'])) {
    $texte = $_POST['query'];
    $resultats = rechercherUtilisateurs($texte, $bdd);
    
    if(count($resultats) > 0) {
        foreach ($resultats as $utilisateur) {
            echo '<div class="search-results">';
            echo '        <div class="result-item">';
            echo '            <img src="' . htmlspecialchars($utilisateur['photo_profil']) . '" alt="Profil" class="result-avatar">';
            echo '            <div class="result-content">';
            echo '                <h3>' . htmlspecialchars($utilisateur['nom']) . ' ' . htmlspecialchars($utilisateur['prenom']) . '</h3>';
            echo '                <p>' . htmlspecialchars($utilisateur['bio']) . '</p>'; 
            echo '            </div>';
            if (isset($_SESSION['id_utilisateur']) && $_SESSION['est_admin'] == 1) {
                echo '    <div class="admin-controls">';
                echo '        <button class="delete-user-btn" data-user-id="' . htmlspecialchars($utilisateur['id_utilisateur']) . '">Supprimer</button>';
                echo '        <button class="block-user-btn" data-user-id="' . htmlspecialchars($utilisateur['id_utilisateur']) . '" data-block="1">Bloquer</button>';                
                echo '    </div>';
            }
            echo '        </div>';
            echo '</div>';
        }
    } else {
        echo "Aucun résultat trouvé.";
    }
}
?>