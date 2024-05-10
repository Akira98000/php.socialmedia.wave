<?php
require_once "../model/function.php";

verifierConnexion();
$userId = isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : '';

if ($userId) {
    $publicationsParPage = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $debut = ($page - 1) * $publicationsParPage;
    $infosUtilisateur = chargerInfosUtilisateur($bdd, $userId, $debut, $publicationsParPage);
    if ($infosUtilisateur) {
        extract($infosUtilisateur);
        $est_admin = $infosUtilisateur['est_admin'];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wave &copy;</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/> <!-- IMPORT POUR LES ICONS-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fetch/3.0.0/fetch.min.js"></script>

</head>
<body>
    <div id="loadingScreen" class="loading-screen">
        <img src="images/icon_wave.png" alt="Logo" class="logo">
        <div class="loader"></div>
    </div> 
    <div class="sidebar">
        <a href="main.php" id="pageLink">
            <div class="sidebarOption">
                <span class="material-icons">home</span>
                <h2>Fil d'actualité</h2>
            </div>
        </a>
        <a href="search.php" id="pageLink">
            <div class="sidebarOption">
                <span class="material-icons">search</span>
                <h2>Rechercher</h2>
            </div>
        </a>
        <a href="FiltrerPost.php" id="pageLink">
            <div class="sidebarOption">
                <span class="material-icons">tune</span>
                <h2>Filtrer</h2>
            </div>
        </a>
        <a href="notification.php" id="pageLink">
            <div class="sidebarOption">
                <span class="material-icons">notifications_none</span>
                <h2>Notifications</h2>
            </div>
        </a>
        <a href="message.php" id="pageLink">
            <div class="sidebarOption">
                <span class="material-icons">mail_outline</span>
                <h2>Messages</h2>
            </div>
        </a>
        <a href="enregistrement.php" id="pageLink">
            <div class="sidebarOption">
                <span class="material-icons">bookmark_border</span>
                <h2>Enregistrement</h2>
            </div>
        </a>
        <a href="amis.php" id="pageLink">
            <div class="sidebarOption">
                <span class="material-icons">list_alt</span>
                <h2>Amis</h2>
            </div>
        </a>
        <a href="publier.php" id="pageLink">
            <div class="sidebarOption">
                <span class="material-icons">publish</span>
                <h2>Publier</h2>
            </div>
        </a>
        
        <a href="profil.php" id="pageLink">
            <div class="sidebarOption">
                <span class="material-icons">perm_identity</span>
                <h2>Mon Profil</h2>
            </div>
        </a>
        <?php if($est_admin==1){
            echo "<a href=\"admin.php\" id='pageLink'>";
            echo "<div class=\"sidebarOption\">";
            echo "<span class=\"material-icons\">dashboard</span>";
            echo "<h2>admin</h2>";
            echo "</div>";
            echo "</a>";
        }?>
        <a href="deconnexion.php" id="pageLink" >
            <div class="sidebarOption">
                <span class="material-icons">cancel_presentation</span>
                <h2>Déconnexion</h2>
            </div>
        </a>
    </div>