<?php
require_once "../model/function.php";

verifierConnexion();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    
    $result = deletePost($bdd, $post_id);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => $post_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression du post.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
}
?>
