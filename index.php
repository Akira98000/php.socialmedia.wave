<?php
include 'app/client/model/function.php'; 

if(estUtilisateurConnecte()) {
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    $vueClientPath = 'app/client/vue/';

    switch ($page) {
        case 'home':
            include $vueClientPath . 'main.php';
            break;
        case 'login':
            include $vueClientPath . 'login.php';
            break;
        case 'signup':
            include $vueClientPath . 'signup.php';
            break;
        default:
            include $vueClientPath . 'erreur.php';
            break;
    }
} else {
    header("Location: app/client/vue/login.php");
    exit();
}
?>
