<?php
require_once '../model/function.php';

header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idUtilisateur = $_POST['userId'];
    $idPublication = $_POST['postId'];

    if (estPublicationEnregistree($bdd, $idUtilisateur, $idPublication)) {
        $resultat = supprimerPublicationPreferee($bdd, $idUtilisateur, $idPublication);
        $action = 'supprimee';
    } else {
        $resultat = ajouterPublicationPreferee($bdd, $idUtilisateur, $idPublication);
        $action = 'ajoutee';
    }
    
    $reponse = [
        'success' => $resultat,
        'action' => $action
    ];
    echo json_encode($reponse);
}
?>
