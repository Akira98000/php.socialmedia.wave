<?php
require_once "../model/function.php";

verifierConnexion();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    
    $result = deleteUser($bdd, $userId);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => $userId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression du post.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
}
?>
