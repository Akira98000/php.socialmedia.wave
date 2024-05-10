<?php 

require 'config.php';
include 'config.php';

global $bdd;
global $utilisateur;

session_start();
setlocale(LC_TIME, 'fr_FR.UTF-8', 'French_France.1252');
$bdd = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8", $username, $password);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function rechercherUtilisateurs($texte, $bdd) {
    try {
        $query = $bdd->prepare("SELECT * FROM Utilisateurs WHERE nom LIKE :texte OR prenom LIKE :texte");
        $likeTexte = '%'.$texte.'%';
        $query->bindParam(':texte', $likeTexte, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        
        return [];
    }
}

// FONCTION : LOGINUSER / PERMET DE SE CONNECTER
function loginUser($bdd, $email, $mdp) {
    try {
        $stmt = $bdd->prepare("SELECT * FROM Utilisateurs WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $utilisateur = $stmt->fetch();

        if ($utilisateur['status'] === 'bloquer') {
            header("Location: blocked.php");
            exit();
        }

        if ($utilisateur && $mdp === $utilisateur['mot_de_passe']) {
            session_start();
            $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
            $_SESSION['nom'] = $utilisateur['nom'];
            $_SESSION['prenom'] = $utilisateur['prenom'];
            $_SESSION['email'] = $utilisateur['email'];
            $_SESSION['logged_in'] = false;
            $_SESSION['est_admin'] = $utilisateur['est_admin'];
            
            if($_SESSION['est_admin'] == 1){
                header("Location: admin.php");
            } else {
                header("Location: profil.php");
            }
            exit();
        } else {
            return "Adresse e-mail ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        return "Erreur : " . $e->getMessage();
    }
}

// FONCTION : REGISTERUSER / PERMET DE S'INSCRIRE A WAVE
function registerUser($bdd, $nom, $prenom, $adr_mail, $mdp, $datenaissance, $genre, $ville, $pays, $bio) {
    try {
        $stmt = $bdd->prepare("INSERT INTO Utilisateurs (nom, prenom, email, mot_de_passe, date_de_naissance, genre, ville, pays, bio, photo_profil, est_admin, type_compte, date_inscription, status) VALUES (:nom, :prenom, :email, :mot_de_passe, :date_de_naissance, :genre, :ville, :pays, :bio, 'uploads/default.png', 0, 'Particulier', current_timestamp(), 'public')");
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":prenom", $prenom);
        $stmt->bindParam(":email", $adr_mail);
        $stmt->bindParam(":mot_de_passe", $mdp);
        $stmt->bindParam(":date_de_naissance", $datenaissance);
        $stmt->bindParam(":genre", $genre);
        $stmt->bindParam(":ville", $ville);
        $stmt->bindParam(":pays", $pays);
        $stmt->bindParam(":bio", $bio);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        return "Erreur lors de l'inscription : " . $e->getMessage();
    }
}

// FONCTION : GETUSERINFO / RECUPERER LES INFO DU USER
function getUserInfo($bdd, $userId) {
    try {
        $stmt = $bdd->prepare("SELECT * FROM Utilisateurs WHERE id_utilisateur = :id_utilisateur");
        $stmt->bindParam(":id_utilisateur", $userId);
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        return null;
    }
}


// FONCTION : ESTUTILISATEURCONNECTE / VERIFIE SI UTILISATEUR CONNECTER 
function estUtilisateurConnecte() {
    if(isset($_SESSION['utilisateur_connecte']) && $_SESSION['utilisateur_connecte'] === true) {
        return true;
    } else {
        return false;
    }
}

// FONCTION : VERIFIERSESSION / VERIFIE SI L'USER EST LA
function verifierSession() {
    if (isset($_SESSION['id_utilisateur'])) {
        header("Location: profil.php"); 
        exit();
    }
}

// FONCTION : VERIFIERCONNEXION / VERIFIE SI L'USER EST LA
function verifierConnexion() {
    if (!isset($_SESSION['id_utilisateur'])) {
        header("Location: login.php");
        exit();
    }
}

// FONCTION : TRAITERSOUMISSIONFORMULAIRE / VERIFIE SI LOG EST BON
function traiterSoumissionFormulaire($bdd) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $mdp = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';

        if (!empty($email) && !empty($mdp)) {
            return loginUser($bdd, $email, $mdp);
        } else {
            return "Veuillez remplir tous les champs.";
        }
    }
    return null;
}

function traitementSoumissionFormulaireSignUp($bdd) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adr_mail = $_POST['email'];
        $mdp = $_POST['mot_de_passe'];
        $genre = $_POST['genre'];
        $ville = $_POST['ville'];
        $pays = $_POST['pays'];
        $datenaissance =$_POST['date'];
        $bio = $_POST['bio'];
        $type_compte = $_POST['type_compte'];
        $est_admin = $_POST['est_admin'];
        $photo_profil = $_FILES['photo_profil']['name']; 

        if (registerUser($bdd, $nom, $prenom, $adr_mail, $mdp, $datenaissance, $genre, $ville, $pays, $bio, $photo_profil, $est_admin, $type_compte)) {
            header("Location: login.php");
            $messageErreur = "Compte crée avec succès";
            exit();
        } else {
            $erreur = true;
            $messageErreur = "Erreur lors de la création du compte.";
        }
    }
}

function recupererInfosUtilisateurParId($bdd, $idUtilisateur) {
    try {
        $requete = $bdd->prepare("SELECT * FROM Utilisateurs WHERE id_utilisateur = :idUtilisateur");
        $requete->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $requete->execute();

        return $requete->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

// FONCTION : CHARGERINFOUTILISATEUR / LOAD LES INFORMATION USER
function chargerInfosUtilisateur($bdd, $userId) {
    $utilisateur = getUserInfo($bdd, $userId);
    if ($utilisateur) {
        return [
            'nom' => $utilisateur['nom'],
            'prenom' => $utilisateur['prenom'],
            'pays' => $utilisateur['pays'],
            'ville' => $utilisateur['ville'],
            'datenaissance' => $utilisateur['date_de_naissance'],
            'genre' => $utilisateur['genre'],
            'bio' => $utilisateur['bio'],
            'est_admin' => $utilisateur['est_admin'],
            'photo_profil' => $utilisateur['photo_profil'],
            'publications' => getAllUserPublications($bdd, $userId),
            'totalPosts' => countUserPosts($bdd, $userId),
            'suivi' => countUserFollowing($bdd, $userId),
            'follower' => countUserFollowers($bdd, $userId),
            'nonFriends' => getNonFriends($bdd, $userId), 
            'enregistrer' => getPublicationsEnregistrees($bdd, $userId) 
        ];
    } else {
        return false;
    }
}

function getNombreTotalPages($bdd, $userId, $publicationsParPage) {
    try {
        $followingQuery = "SELECT id_utilisateur FROM following WHERE id_follower = :userId";
        $followingStmt = $bdd->prepare($followingQuery);
        $followingStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $followingStmt->execute();
        $followingUsers = $followingStmt->fetchAll(PDO::FETCH_COLUMN);
        if (empty($followingUsers)) {
            return 0;
        }
        $followingUsersString = implode(',', array_map('intval', $followingUsers));
        $postsCountQuery = "SELECT COUNT(*) FROM post WHERE id_utilisateur IN ($followingUsersString)";
        $postsCountStmt = $bdd->query($postsCountQuery);
        $nombreTotalPublications = $postsCountStmt->fetchColumn();
        return ceil($nombreTotalPublications / $publicationsParPage);
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return 0;
    }
}


function getAllUserPublications($bdd, $userId) {
    try {
        $stmt = $bdd->prepare("SELECT * FROM post WHERE id_utilisateur = :userId ORDER BY date_creation DESC");
        $stmt->bindParam(":userId", $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        return array();
    }
}

function getPublicationsEnregistrees($bdd, $userId) {
    try {
        $stmt = $bdd->prepare("SELECT p.* FROM post p JOIN publications_preferees pp ON p.id = pp.id_publication WHERE pp.id_utilisateur = :idUtilisateur ORDER BY pp.date_enregistrement DESC");
        $stmt->bindParam(":idUtilisateur", $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des publications enregistrées : " . $e->getMessage();
        return [];
    }
}

function recupererImageProfil($userId, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT photo_profil FROM Utilisateurs WHERE id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && !empty($result['photo_profil'])) {
            return $result['photo_profil'];
        } else {
            return 'uploads/default.png';
        }
    } catch (PDOException $e) {
        return 'uploads/default.png'; 
    }
}

function traiterLikePublication($bdd, $publicationId, $userId) {
    return likePublication($bdd, $publicationId, $userId);
}

function traiterCommentairePublication($bdd, $publicationId, $userId, $commentaire) {
    return CommentPublication($bdd, $publicationId, $userId, $commentaire);
}

function createNotification($from_user_id, $to_user_id, $msg, $post_id = 0){
    global $bdd;
    $query = "INSERT INTO notifications (from_user_id, to_user_id, message, post_id) VALUES ($from_user_id, $to_user_id, '$msg', $post_id)";
    mysqli_query($db, $query);
}

function getComments($post_id){
    global $bdd;
    $query="SELECT * FROM comments WHERE post_id=$post_id ORDER BY id DESC";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_all($run,true);
}

function getNotifications($current_user_id){
    global $db;
    $query = "SELECT * FROM notifications WHERE to_user_id = $current_user_id ORDER BY id DESC";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
}

function show_time($time){
    return '<time style="font-size:small" class="timeago text-muted text-small" datetime="'.$time.'"></time>';
}

function getLikes($post_id){
    global $bdd;
    $query="SELECT * FROM likes WHERE post_id=$post_id";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_all($run,true);
}

function unlike($post_id){
    global $bdd;
    $query="DELETE FROM likes WHERE user_id=$current_user && post_id=$post_id";
    $poster_id = getPosterId($post_id);
    if($poster_id!=$current_user){
        createNotification($current_user,$poster_id,"unliked your post !",$post_id);
    }
    return mysqli_query($db,$query);
}

function ajouterPublicationPreferee($bdd, $userId, $post_id) {
    try {
        $stmt = $bdd->prepare("INSERT INTO publications_preferees (id_utilisateur, id_publication) VALUES (:idUtilisateur, :idPublication)");
        $stmt->bindParam(":idUtilisateur", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":idPublication", $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return $bdd->lastInsertId();
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout de la publication préférée : " . $e->getMessage();
        return false;
    }
}

function supprimerPublicationPreferee($bdd, $idUtilisateur, $idPublication) {
    try {
        $stmt = $bdd->prepare("DELETE FROM publications_preferees WHERE id_utilisateur = :idUtilisateur AND id_publication = :idPublication");
        $stmt->bindParam(":idUtilisateur", $idUtilisateur, PDO::PARAM_INT);
        $stmt->bindParam(":idPublication", $idPublication, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression de la publication préférée : " . $e->getMessage();
        return false;
    }
}

function estPublicationEnregistree($bdd, $userId, $post_id) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM publications_preferees WHERE id_utilisateur = :idUtilisateur AND id_publication = :idPublication");
        $stmt->bindParam(":idUtilisateur", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":idPublication", $post_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0; 
    } catch (PDOException $e) {
        echo "Erreur lors de la vérification de l'enregistrement : " . $e->getMessage();
        return false;
    }
}

function unfollowUser($user_id){
    global $db;
    $query="DELETE FROM follow_list WHERE follower_id=$current_user && user_id=$user_id";
    createNotification($current_user,$user_id,"Unfollowed you !");
    return mysqli_query($db,$query); 
}

function showError($field){
    if(isset($_SESSION['error'])){
        $error =$_SESSION['error'];
        if(isset($error['field']) && $field==$error['field']){
           ?>
    <div class="alert alert-danger my-2" role="alert">
      <?=$error['msg']?>
    </div>
           <?php
        }
    }
}
   
function showFormData($field){
    if(isset($_SESSION['formdata'])){
        $formdata =$_SESSION['formdata'];
        return $formdata[$field];
    }
}

function isEmailRegistered($email){
    global $db;
    $query="SELECT count(*) as row FROM users WHERE email='$email'";
    $run=mysqli_query($db,$query);
    $return_data = mysqli_fetch_assoc($run);
    return $return_data['row'];
}

function isUsernameRegistered($username){
    global $db;
    $query="SELECT count(*) as row FROM users WHERE username='$username'";
    $run=mysqli_query($db,$query);
    $return_data = mysqli_fetch_assoc($run);
    return $return_data['row'];
}

function checkUser($login_data){
    global $db;
    $username_email = $login_data['username_email'];
    $password=md5($login_data['password']);
    $query = "SELECT * FROM users WHERE (email='$username_email' || username='$username_email') && password='$password'";
    $run = mysqli_query($db,$query);
    $data['user'] = mysqli_fetch_assoc($run)??array();
    if(count($data['user'])>0){
        $data['status']=true;
    }
    else{
        $data['status']=false;
    }
    return $data;
}


function followUser($user_id){
    global $db;
    $cu = getUser($_SESSION['userdata']['id']);
    $current_user=$_SESSION['userdata']['id'];
    $query="INSERT INTO follow_list(follower_id,user_id) VALUES($current_user,$user_id)";
  
    createNotification($cu['id'],$user_id,"started following you !");
    return mysqli_query($db,$query);
    
}



function getLikesCountForPublication($bdd, $publicationId) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM likes WHERE id_article = :publicationId");
        $stmt->bindParam(":publicationId", $publicationId);
        $stmt->execute();
        $likesCount = $stmt->fetchColumn();
        return $likesCount;
    } catch (PDOException $e) {
        return 0; 
    }
}


function isPublicationLiked($bdd, $publicationId, $userId) {
    try {
        $stmt_check = $bdd->prepare("SELECT * FROM likes WHERE id_article = :publicationId AND id_utilisateur = :userId");
        $stmt_check->bindParam(":publicationId", $publicationId);
        $stmt_check->bindParam(":userId", $userId);
        $stmt_check->execute();
        return $stmt_check->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

function isPublicationDisliked($bdd, $publicationId, $userId) {
    try {
        $stmt = $bdd->prepare("SELECT * FROM dislikes WHERE id_article = :id_article AND id_utilisateur = :id_utilisateur");
        $stmt->bindParam(":id_article", $publicationId);
        $stmt->bindParam(":id_utilisateur", $userId);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}


function likePublication($bdd, $publicationId, $userId) {
    try {
        if (isPublicationDisliked($bdd, $publicationId, $userId)) {
            removeDislike($bdd, $publicationId, $userId);
        }
        if (!isPublicationLiked($bdd, $publicationId, $userId)) {
            $stmt = $bdd->prepare("INSERT INTO likes (id_article, id_utilisateur) VALUES (:publicationId, :userId)");
            $stmt->bindParam(":publicationId", $publicationId);
            $stmt->bindParam(":userId", $userId);
            $stmt->execute();
            return true;
        } else {
            return false; 
        }
    } catch (PDOException $e) {
        return false;
    }
}


function unlikePublication($bdd, $publicationId, $userId) {
    try {
        if (isPublicationLiked($bdd, $publicationId, $userId)) {
            $stmt = $bdd->prepare("DELETE FROM likes WHERE id_article = :publicationId AND id_utilisateur = :userId");
            $stmt->bindParam(":publicationId", $publicationId);
            $stmt->bindParam(":userId", $userId);
            $stmt->execute();
            return true; 
        } else {
            return false; 
        }
    } catch (PDOException $e) {
        return false;
    }
}

function addDislike($bdd, $publicationId, $userId) {
    try {
        if (isPublicationLiked($bdd, $publicationId, $userId)) {
            unlikePublication($bdd, $publicationId, $userId); 
        }
        if (!isPublicationDisliked($bdd, $publicationId, $userId)) {
            $stmt = $bdd->prepare("INSERT INTO dislikes (id_article, id_utilisateur) VALUES (:id_article, :id_utilisateur)");
            $stmt->bindParam(":id_article", $publicationId);
            $stmt->bindParam(":id_utilisateur", $userId);
            $stmt->execute();
            return true;
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}


function removeDislike($bdd, $publicationId, $userId) {
    try {
        if (isPublicationDisliked($bdd, $publicationId, $userId)) {
            $stmt = $bdd->prepare("DELETE FROM dislikes WHERE id_article = :id_article AND id_utilisateur = :id_utilisateur");
            $stmt->bindParam(":id_article", $publicationId);
            $stmt->bindParam(":id_utilisateur", $userId);
            $stmt->execute();
            return true;
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

function getDislikesCountForPublication($bdd, $publicationId) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM dislikes WHERE id_article = :id_article");
        $stmt->bindParam(":id_article", $publicationId);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function postComment($bdd, $publicationId, $userId, $comment) {
    try {
        $stmt = $bdd->prepare("INSERT INTO comments (publication_id, user_id, comment) VALUES (:publicationId, :userId, :comment)");
        $stmt->bindParam(":publicationId", $publicationId);
        $stmt->bindParam(":userId", $userId);
        $stmt->bindParam(":comment", $comment);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        error_log("Error in postComment: " . $e->getMessage());
        return false;
    }
}




function CommentPublication($bdd, $publicationId, $userId, $commentaire) {
    try {
        $stmt = $bdd->prepare("INSERT INTO commentaires (id_article, id_utilisateur, commentaire, date_creation) VALUES (:id_article, :id_utilisateur, :commentaire, current_timestamp())");
        $stmt->bindParam(":id_article", $publicationId);
        $stmt->bindParam(":id_utilisateur", $userId);
        $stmt->bindParam(":commentaire", $commentaire);
        $stmt->execute();
        $commentId = $bdd->lastInsertId();
        return ($commentId > 0);
    } catch (PDOException $e) {
        return false;
    }
}


function getCommentCountForPublication($bdd, $publicationId) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM commentaires WHERE id_article = :id_article");
        $stmt->bindParam(":id_article", $publicationId);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count;
    } catch (PDOException $e) {
        return false;
    }
}

function isPublicationCommented($bdd, $publicationId, $userId) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM commentaires WHERE id_article = :id_article AND id_utilisateur = :id_utilisateur");
        $stmt->bindParam(":id_article", $id_article);
        $stmt->bindParam(":id_utilisateur", $id_utilisateur);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return ($count > 0);
    } catch (PDOException $e) {
        return false;
    }
}

function deleteComment($bdd, $id_commentaire) {
    try {
        $stmt = $bdd->prepare("DELETE FROM commentaires WHERE id = :id_commentaire");
        $stmt->bindParam(":id_commentaire", $id_commentaire);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function countUserPosts($bdd, $userId) {
    try {
        $query = "SELECT COUNT(*) as total_posts FROM post WHERE id_utilisateur = :userId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && isset($result['total_posts'])) {
            return $result['total_posts'];
        } else {
            return 0;
        }
    } catch (PDOException $e) {
        return -1;
    }
}

function addFriend($bdd, $userId, $friendId) {
    try {
        $query = "SELECT * FROM following WHERE id_follower = :userId AND id_utilisateur = :friendId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':friendId', $friendId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $query = "INSERT INTO following (id_follower, id_utilisateur) VALUES (:userId, :friendId)";
            $stmt = $bdd->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':friendId', $friendId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return false;
    }
}

function removeFriend($bdd, $userId, $friendId) {
    try {
        $query = "DELETE FROM following WHERE id_follower = :userId AND id_utilisateur = :friendId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':friendId', $friendId, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function getFriendsList($bdd, $userId) {
    try {
        $query = "SELECT id_utilisateur FROM following WHERE id_follower = :userId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $friends = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $friends[] = $row['id_utilisateur'];
        }
        return $friends;
    } catch (PDOException $e) {
        return [];
    }
}

function sontAmis($bdd, $userId, $friendId) {
    try {
        $query = "SELECT * FROM following WHERE id_follower = :userId AND id_utilisateur = :friendId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':friendId', $friendId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

function getNonFriends($bdd, $userId) {
    $query = "
        SELECT *
        FROM Utilisateurs
        WHERE id_utilisateur != :userId
        AND id_utilisateur NOT IN (
            SELECT id_utilisateur
            FROM following
            WHERE id_follower = :userId
        )
    ";

    $stmt = $bdd->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countUserFollowing($bdd, $userId) {
    try {
        $query = "SELECT COUNT(*) FROM following WHERE id_follower = :userId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count;
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return 0;
    }
}

function countUserFollowers($bdd, $userId) {
    try {
        $query = "SELECT COUNT(*) FROM following WHERE id_utilisateur = :userId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count;
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return 0;
    }
}

function getFollowers($bdd, $userId) {
    try {
        $query = "SELECT Utilisateurs.id_utilisateur, Utilisateurs.nom, Utilisateurs.prenom, 
                         Utilisateurs.photo_profil, Utilisateurs.bio, Utilisateurs.email, 
                         (SELECT COUNT(*) FROM following WHERE id_utilisateur = :userId AND id_follower = Utilisateurs.id_utilisateur) AS isMutual
                  FROM Utilisateurs
                  INNER JOIN following ON Utilisateurs.id_utilisateur = following.id_follower
                  WHERE following.id_utilisateur = :userId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $followers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $followers;
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return [];
    }
}

function getFollowing($bdd, $userId) {
    try {
        $query = "SELECT Utilisateurs.id_utilisateur, Utilisateurs.nom, Utilisateurs.prenom, 
                         Utilisateurs.photo_profil, Utilisateurs.bio, Utilisateurs.email, 
                         (SELECT COUNT(*) FROM following WHERE id_utilisateur = Utilisateurs.id_utilisateur AND id_follower = :userId) AS isMutual
                  FROM Utilisateurs
                  INNER JOIN following ON Utilisateurs.id_utilisateur = following.id_utilisateur
                  WHERE following.id_follower = :userId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $following = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $following;
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return [];
    }
}



function getFollowingPublications($bdd, $userId, $page, $publicationsParPage) {
    try {
        $page = max(1, $page);
        $offset = ($page - 1) * $publicationsParPage;

        $followingQuery = "SELECT id_utilisateur FROM following WHERE id_follower = :userId";
        $followingStmt = $bdd->prepare($followingQuery);
        $followingStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $followingStmt->execute();
        $followingUsers = $followingStmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($followingUsers)) {
            return [];
        }

        $followingUsersString = implode(',', array_map('intval', $followingUsers));
        $postsQuery = "SELECT post.*, Utilisateurs.nom, Utilisateurs.photo_profil ,Utilisateurs.prenom,
                              (SELECT COUNT(*) FROM likes WHERE likes.id_article = post.id) AS nombre_likes,
                              (SELECT COUNT(*) FROM commentaires WHERE commentaires.id_article = post.id) AS nombre_commentaires
                       FROM post
                       INNER JOIN Utilisateurs ON post.id_utilisateur = Utilisateurs.id_utilisateur
                       WHERE post.id_utilisateur IN ($followingUsersString)
                       ORDER BY post.date_creation DESC
                       LIMIT :offset, :publicationsParPage";
        $postsStmt = $bdd->prepare($postsQuery);
        $postsStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $postsStmt->bindParam(':publicationsParPage', $publicationsParPage, PDO::PARAM_INT);
        $postsStmt->execute();

        return $postsStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return [];
    }
}

function FiltrerFollowingPublications($bdd, $userId, $page, $publicationsParPage, $dateDebut, $dateFin) {
    try {
        $page = max(1, $page);
        $offset = ($page - 1) * $publicationsParPage;

        $followingQuery = "SELECT id_utilisateur FROM following WHERE id_follower = :userId";
        $followingStmt = $bdd->prepare($followingQuery);
        $followingStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $followingStmt->execute();
        $followingUsers = $followingStmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($followingUsers)) {
            return [];
        }

        $followingUsersString = implode(',', array_map('intval', $followingUsers));
        
        // Modifiez ici la requête pour inclure le filtrage par date
        $postsQuery = "SELECT post.*, Utilisateurs.nom, Utilisateurs.photo_profil, Utilisateurs.prenom,
                              (SELECT COUNT(*) FROM likes WHERE likes.id_article = post.id) AS nombre_likes,
                              (SELECT COUNT(*) FROM commentaires WHERE commentaires.id_article = post.id) AS nombre_commentaires
                       FROM post
                       INNER JOIN Utilisateurs ON post.id_utilisateur = Utilisateurs.id_utilisateur
                       WHERE post.id_utilisateur IN ($followingUsersString)
                       AND post.date_creation BETWEEN :dateDebut AND :dateFin
                       ORDER BY post.date_creation DESC
                       LIMIT :offset, :publicationsParPage";
        $postsStmt = $bdd->prepare($postsQuery);
        
        // Convertissez les dates en format Y-m-d pour la compatibilité SQL
        $dateDebutFormatted = date('Y-m-d', strtotime($dateDebut)) . " 00:00:00";
        $dateFinFormatted = date('Y-m-d', strtotime($dateFin)) . " 23:59:59";

        $postsStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $postsStmt->bindParam(':publicationsParPage', $publicationsParPage, PDO::PARAM_INT);
        $postsStmt->bindParam(':dateDebut', $dateDebutFormatted);
        $postsStmt->bindParam(':dateFin', $dateFinFormatted);
        $postsStmt->execute();

        return $postsStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return [];
    }
}


function getInfo($bdd,$userId){
        try {
            $stmt = $bdd->prepare("SELECT * FROM Utilisateurs WHERE id_utilisateur = :id_utilisateur");
            $stmt->bindParam(":id_utilisateur", $userId);
            $stmt->execute();
            $utilisateur = $stmt->fetch();
            return $utilisateur;
        } catch (PDOException $e) {
            echo "Erreur de base de données : " . $e->getMessage();
            return null;
        }
    }


function getMessages($db,$id_Utilisateur1, $id_Utilisateur2) {
    $query = "SELECT * FROM messages WHERE 
                (sender_id = :id_Utilisateur1 AND receiver_id = :id_Utilisateur2) OR 
                (sender_id = :id_Utilisateur2 AND receiver_id = :id_Utilisateur1) 
                ORDER BY time_sent ASC";

    try {
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_Utilisateur1', $id_Utilisateur1, PDO::PARAM_INT);
        $stmt->bindParam(':id_Utilisateur2', $id_Utilisateur2, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Erreur d'exécution de la requête: " . $e->getMessage();
        return [];
    }
}

function envoyerMessage($db,$idExpeditateur, $idDestinataire, $messageTexte) {
    $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (:idExpeditateur, :idDestinataire, :messageTexte)";
    try {
        $stmt = $db->prepare($query);
        $stmt->bindParam(':idExpeditateur', $idExpeditateur, PDO::PARAM_INT);
        $stmt->bindParam(':idDestinataire', $idDestinataire, PDO::PARAM_INT);
        $stmt->bindParam(':messageTexte', $messageTexte, PDO::PARAM_STR);
        $stmt->execute();
    } catch(PDOException $e) {
        echo "Erreur lors de l'envoi du message: " . $e->getMessage();
    }
}

function groupMessagesByDate($messages) {
    $groupedMessages = [];
    foreach ($messages as $message) {
        $date = date('Y-m-d', strtotime($message['time_sent'])); // Extraire la date
        $time = date('H:i', strtotime($message['time_sent'])); // Extraire l'heure
        $groupedMessages[$date][] = [
            'message' => $message['message'],
            'time' => $time,
            'sender_id' => $message['sender_id']
        ];
    }
    return $groupedMessages;
}

function deconnecter() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: login.php");
    exit();
}

function updateProfile($bdd, $bio, $pays, $ville, $datenaissance, $genre, $userId) {
    try {
        $stmt = $bdd->prepare("UPDATE Utilisateurs SET bio = :bio, pays = :pays, ville = :ville, date_de_naissance = :datenaissance, genre = :genre WHERE id_utilisateur = :id_utilisateur");
        $stmt->bindParam(":bio", $bio);
        $stmt->bindParam(":pays", $pays);
        $stmt->bindParam(":ville", $ville);
        $stmt->bindParam(":datenaissance", $datenaissance);
        $stmt->bindParam(":genre", $genre);
        $stmt->bindParam(":id_utilisateur", $userId);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}

function uploadProfilePhoto($bdd, $userId) {
    $target_dir = "uploads/";
    $original_file_name = basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . $original_file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check === false) {
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        $file_extension = pathinfo($original_file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid() . "." . $file_extension; 
        $target_file = $target_dir . $new_file_name;
    }

    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $uploadOk = 0;
    }

    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedExtensions)) {
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "Le fichier ". htmlspecialchars($original_file_name). " a été téléchargé.";
            $stmt = $bdd->prepare("UPDATE Utilisateurs SET photo_profil = :photo_profil WHERE id_utilisateur = :id_utilisateur");
            $stmt->bindParam(":photo_profil", $target_file);
            $stmt->bindParam(":id_utilisateur", $userId);
            if ($stmt->execute()) {
                header("Location: profil.php");
                exit;
            }
        } 
    }
}


function uploadPostImage($bdd, $userId, $postImage, $location, $comment) {
    $target_dir = "uploads/post/";
    $target_file = "";
    
    if (!empty($_FILES["postImage"]["name"])) {
        $original_file_name = basename($_FILES["postImage"]["name"]);
        $target_file = $target_dir . uniqid() . '-' . $original_file_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png", "gif", "mp4", "avi", "mov");
        if (!in_array($imageFileType, $allowedExtensions) || $_FILES["postImage"]["size"] > 5000000) {
            $uploadOk = 0;
        }

        if ($uploadOk && move_uploaded_file($_FILES["postImage"]["tmp_name"], $target_file)) {
        } else {
            $target_file = "";
        }
    }

    try {
        if (!empty($target_file)) {
            $stmt = $bdd->prepare("INSERT INTO post (id_utilisateur, image_article, texte_article, lieu, date_creation) VALUES (:id_utilisateur, :image_article, :texte_article, :lieu, current_timestamp())");
            $stmt->bindParam(":id_utilisateur", $userId);
            $stmt->bindParam(":image_article", $target_file);
        } else {
            $stmt = $bdd->prepare("INSERT INTO post (id_utilisateur, texte_article, lieu, date_creation) VALUES (:id_utilisateur, :texte_article, :lieu, current_timestamp())");
            $stmt->bindParam(":id_utilisateur", $userId);
        }
        
        $stmt->bindParam(":texte_article", $comment);
        $stmt->bindParam(":lieu", $location);

        if ($stmt->execute()) {
            header("Location: profil.php");
            exit;
        }
    } catch (PDOException $e) {
        return "Erreur lors de la publication : " . $e->getMessage();
    }
}





function deleteUser($bdd, $userId) {
    try {
        $stmt = $bdd->prepare("DELETE FROM Utilisateurs WHERE id_utilisateur = :user_id");
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        return true; 
    } catch (PDOException $e) {
        return false; 
    }
}

function blockUser($bdd, $userId) {
    try {
        $stmt = $bdd->prepare("UPDATE Utilisateurs SET status = 'bloquer' WHERE id_utilisateur = :user_id");
        $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Utilisateur bloqué avec succès.'];
        } else {
            return ['success' => false, 'message' => "L'utilisateur n'a pas pu être bloqué ou n'existe pas."];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => "Erreur lors du blocage de l'utilisateur : " . $e->getMessage()];
    }
}


// function getNotificationsUtilisateur($bdd, $idUtilisateur) {
//     try {
//         $stmt = $bdd->prepare("SELECT * FROM notifications WHERE id_utilisateur = :idUtilisateur ORDER BY date_notification DESC");
//         $stmt->bindParam(":idUtilisateur", $idUtilisateur);
//         $stmt->execute();
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);

//         $updateStmt = $bdd->prepare("UPDATE notifications SET est_vue = 1 WHERE id_utilisateur = :idUtilisateur");
//         $updateStmt->bindParam(":idUtilisateur", $idUtilisateur);
//         $updateStmt->execute();

//         return $notifications;

//     } catch (PDOException $e) {
//         echo "Erreur lors de la récupération des notifications : " . $e->getMessage();
//         return [];
//     }
// }

function getNotificationsUtilisateur($bdd, $idUtilisateur) {
    try {
        $stmt = $bdd->prepare("
            SELECT n.*, u.nom, u.prenom, u.photo_profil
            FROM notifications n
            JOIN Utilisateurs u ON n.id_utilisateur_cible = u.id_utilisateur
            WHERE n.id_utilisateur = :idUtilisateur
            ORDER BY n.date_notification DESC
        ");
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des notifications avec détails : " . $e->getMessage();
        return [];
    }
}

function getBlockedUsers($bdd) {
    try {
        $stmt = $bdd->prepare("SELECT * FROM Utilisateurs WHERE status = 'bloquer'");
        $stmt->execute();
        $usersBlocked = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $usersBlocked;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des utilisateurs bloqués : " . $e->getMessage());
        return [];
    }
}


function ajouterNotification($bdd, $idUtilisateur, $idUtilisateurCible, $type, $contenu) {
    try {
        $stmt = $bdd->prepare("INSERT INTO notifications (id_utilisateur, id_utilisateur_cible, type_notification, contenu, date_notification, est_vue) VALUES (:idUtilisateur, :idUtilisateurCible, :type, :contenu, NOW(), 0)");
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        $stmt->bindParam(":idUtilisateurCible", $idUtilisateurCible);
        $stmt->bindParam(":type", $type);
        $stmt->bindParam(":contenu", $contenu);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout de la notification : " . $e->getMessage();
        return false;
    }
}

function getPublicationDetails($bdd, $publicationId) {
    try {
        $query = "SELECT post.id, post.id_utilisateur, post.image_article, post.texte_article, 
                         post.date_creation, post.lieu, Utilisateurs.photo_profil ,Utilisateurs.nom, Utilisateurs.prenom,
                         (SELECT COUNT(*) FROM comments WHERE comments.publication_id = post.id) AS nombre_commentaires
                  FROM post
                  JOIN Utilisateurs ON post.id_utilisateur = Utilisateurs.id_utilisateur
                  WHERE post.id = :publicationId";

        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':publicationId', $publicationId, PDO::PARAM_INT);
        $stmt->execute();

        $publicationDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$publicationDetails) {
            return null; 
        }
        return $publicationDetails;
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération des détails de la publication : ' . $e->getMessage());
        return null;
    }
}

function filtrerPublicationsParTemps($bdd, $dateDebut, $dateFin, $idUtilisateur = null) {
    try {
        $query = "SELECT * FROM post WHERE date_creation BETWEEN :dateDebut AND :dateFin";
        if ($idUtilisateur !== null) {
            $query .= " AND id_utilisateur = :idUtilisateur";
        }
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':dateDebut', $dateDebut);
        $stmt->bindParam(':dateFin', $dateFin);
        if ($idUtilisateur !== null) {
            $stmt->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        }
        $stmt->execute();
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resultats;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}


function getCommentsForPublication($bdd, $publicationId) {
    $stmt = $bdd->prepare("SELECT comments.*, Utilisateurs.nom, Utilisateurs.prenom FROM comments JOIN Utilisateurs ON comments.user_id = Utilisateurs.id_utilisateur WHERE publication_id = :publicationId ORDER BY created_at DESC");
    $stmt->execute(['publicationId' => $publicationId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProfileInfo($bdd, $userId) {
    try {
        $userInfoQuery = "SELECT nom, prenom, date_de_naissance, photo_profil, bio, pays, ville FROM Utilisateurs WHERE id_utilisateur = :userId";
        $stmt = $bdd->prepare($userInfoQuery);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userInfo) {
            return null; 
        }

        $publicationsQuery = "SELECT * FROM post WHERE id_utilisateur = :userId";
        $stmt = $bdd->prepare($publicationsQuery);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $userInfo['publications'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $followersCountQuery = "SELECT COUNT(*) FROM following WHERE id_utilisateur = :userId";
        $stmt = $bdd->prepare($followersCountQuery);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $userInfo['followersCount'] = $stmt->fetchColumn();

        $postCountQuery = "SELECT COUNT(*) FROM post WHERE id_utilisateur = :userId";
        $stmt = $bdd->prepare($postCountQuery);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $userInfo['postCount'] = $stmt->fetchColumn();


        $followingsCountQuery = "SELECT COUNT(*) FROM following WHERE id_follower = :userId";
        $stmt = $bdd->prepare($followingsCountQuery);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $userInfo['followingsCount'] = $stmt->fetchColumn();

        return $userInfo;
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return null;
    }
}

function getNonAdminUsers($bdd) {
    global $bdd;
    $query = "SELECT id_utilisateur FROM Utilisateurs WHERE est_admin = 0";
    $stmt = $bdd->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $users;
}

function deletePost($bdd, $post_id) {
    try {
        $query = "DELETE FROM post WHERE id = :post_id";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $success = $stmt->execute();
        if ($success) {
            return ['success' => true];
        } else {
            $errorInfo = $stmt->errorInfo();
            return ['success' => false, 'message' => "Erreur lors de l'exécution de la requête : " . $errorInfo[2]];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => "Exception capturée : " . $e->getMessage()];
    }
}

?>