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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['id_utilisateur']; 
    $postImage = $_FILES["postImage"]["name"]; 
    $location = $_POST["Lieux"]; 
    $comment = $_POST["comment"];
    uploadPostImage($bdd, $userId, $postImage, $location, $comment);
}

?>  
<?php include "header.php"; ?>
<div class="feed">
    <div class="feed__header">
        <h2><?php $formatNomPrenom = '@' . strtolower($prenom . '_' . $nom);
        echo ' '.$formatNomPrenom; ?></h2>
    </div>  
    <div class="search-container">
        <div class="search-header">
            <h2>Publier un Post</h2>
        </div>
        <div class="profile-edit">
            <form enctype="multipart/form-data"  method="post">
                <div class="profile-form-group">
                <div class="profile-center">
                <div class="upload-container centered-content">
                    <label for="publication" class="upload-label">
                        <div class="upload-icon">
                            <img src="https://cdn.pixabay.com/animation/2023/06/13/15/13/15-13-08-190_512.gif" alt="Upload Icon" />
                        </div>
                        <div class="upload-instructions">
                            Cliquez pour choisir une image ou une vidéo depuis votre ordinateur. <br> Si vous ne voulez que du texte alors écrivez simplement dans Description.
                        </div>
                    </label>
                    <input id="publication" type="file" name="postImage" accept=".png, .jpg, .jpeg, video/*" style="display: none;">
                </div>
                </div>
                <script>
                    document.getElementById('publication').addEventListener('change', function() {
                        if (this.files && this.files.length > 0) {
                            var fileName = this.files[0].name;
                            document.querySelector('.upload-instructions').textContent = fileName;
                        }
                    });
                </script>
                <div class="profile-form-group">
                <label for="Lieux">Lieu de la photo prise</label>
                    <select id="Lieux" name="Lieux">
                        <option value="Autriche">Autriche</option>
                        <option value="Belgique">Belgique</option>
                        <option value="Bulgarie">Bulgarie</option>
                        <option value="Chypre">Chypre</option>
                        <option value="Croatie">Croatie</option>
                        <option value="Danemark">Danemark</option>
                        <option value="Espagne">Espagne</option>
                        <option value="Estonie">Estonie</option>
                        <option value="Finlande">Finlande</option>
                        <option value="France">France</option>
                        <option value="Grèce">Grèce</option>
                        <option value="Hongrie">Hongrie</option>
                        <option value="Irlande">Irlande</option>
                        <option value="Italie">Italie</option>
                        <option value="Lettonie">Lettonie</option>
                        <option value="Lituanie">Lituanie</option>
                        <option value="Luxembourg">Luxembourg</option>
                        <option value="Malte">Malte</option>
                        <option value="Pays-Bas">Pays-Bas</option>
                        <option value="Pologne">Pologne</option>
                        <option value="Portugal">Portugal</option>
                        <option value="République tchèque">République tchèque</option>
                        <option value="Roumanie">Roumanie</option>
                        <option value="Slovaquie">Slovaquie</option>
                        <option value="Slovénie">Slovénie</option>
                        <option value="Suède">Suède</option>
                        <option value="Suisse">Suisse</option>
                        <option value="Andorre">Andorre</option>
                        <option value="Monaco">Monaco</option>
                    </select>
                </div>
                <div class="profile-form-group">
                <label for="comment">Description de la publication</label>
                    <input id="comment" name="comment" type="text">
                </div>
                <div class="profile-form-group">
                    <button type="submit" value="Publier" class="profile-submit-btn">Poster ma publication</button>
                </div>
            </form>
        </div>    
    </div>
</div>
</div>
<?php include "header_end.php"; ?>