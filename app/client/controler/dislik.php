<?php
require_once "../model/function.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['post_id'], $data['user_id'])) {
    $postId = $data['post_id'];
    $userId = $data['user_id'];

    if (isPublicationDisliked($bdd, $postId, $userId)) {
        if (removeDislike($bdd, $postId, $userId)) {
            $newDislikeCount = getDislikesCountForPublication($bdd, $postId);
            $newLikeCount = getLikesCountForPublication($bdd, $postId); 
            echo json_encode(['success' => true, 'new_dislike_count' => $newDislikeCount, 'disliked' => false]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        if (addDislike($bdd, $postId, $userId)) {
            $newDislikeCount = getDislikesCountForPublication($bdd, $postId);
            $newLikeCount = getLikesCountForPublication($bdd, $postId); 
            echo json_encode(['success' => true, 'new_dislike_count' => $newDislikeCount, 'disliked' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}
