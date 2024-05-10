<?php
require_once '../model/function.php';

if (isset($_GET['postId'])) {
    $postId = $_GET['postId'];
    $comments = getCommentsByPublicationId($bdd, $postId);
    echo json_encode($comments);
} else {
    echo json_encode([]);
}


?>