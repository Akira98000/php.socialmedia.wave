<?php
require_once '../model/function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $publicationId = isset($_POST['publicationId']) ? $_POST['publicationId'] : null;
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

    if ($publicationId && $comment !== '') {
        $userId = $_SESSION['id_utilisateur']; 
        if (postComment($bdd, $publicationId, $userId, $comment)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

?>