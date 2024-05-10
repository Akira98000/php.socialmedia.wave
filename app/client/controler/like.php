<?php
require_once '../model/function.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['post_id']) && isset($data['user_id'])) {
        $postId = $data['post_id'];
        $userId = $data['user_id'];

        if (isPublicationLiked($bdd, $postId, $userId)) {
            $result = unlikePublication($bdd, $postId, $userId);
        } else {
            $result = likePublication($bdd, $postId, $userId);
        }
        
        if ($result) {
            $likesCount = getLikesCountForPublication($bdd, $postId);
            echo json_encode(['success' => true, 'new_like_count' => $likesCount]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Impossible de traiter la requête.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Données requises manquantes.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée.']);
}
?>
