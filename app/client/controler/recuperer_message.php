<?php

require_once "../model/function.php";

$userId = $_SESSION['id_utilisateur'];
$amiId = isset($_GET['ami']) ? intval($_GET['ami']) : 0;

if ($amiId <= 0) {
    exit("ID d'ami invalide.");
}

$query = "SELECT * FROM messages WHERE (sender_id = :user_id AND receiver_id = :ami_id) OR (sender_id = :ami_id AND receiver_id = :user_id) ORDER BY time_sent ASC";
$stmt = $bdd->prepare($query);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->bindParam(':ami_id', $amiId, PDO::PARAM_INT);

$stmt->execute();
if (!$stmt) {
    echo json_encode(["error" => "Erreur lors de l'exécution de la requête"]);
    exit;
}

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [];
foreach ($messages as $message) {
    $dateObj = new DateTime($message['time_sent']);
    $formattedTime = $dateObj->format('H:i:s');

    $response[] = [
    'sender_id' => $message['sender_id'],
    'text' => $message['message'],
    'time' => $formattedTime,  
    'date' => strftime('%d %B %Y', strtotime($message['time_sent']))
];

}

header('Content-Type: application/json');
echo json_encode($response);

?>