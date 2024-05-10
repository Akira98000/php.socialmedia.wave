<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../model/function.php";

if (isset($_SESSION['id_utilisateur']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['id_utilisateur'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];
    $response = [];

    try {
        $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (:sender_id, :receiver_id, :message)";
        $stmt = $bdd->prepare($query);

        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();
        $response['success'] = true;
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {

        echo "Erreur lors de l'envoi du message : " . $e->getMessage();
        $response['success'] = false;
        $response['error'] = "Message d'erreur";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>

