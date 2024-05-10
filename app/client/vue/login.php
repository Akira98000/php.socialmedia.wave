
<?php
require_once "../model/function.php";

verifierSession();


$erreur = false;
$messageErreur = '';
$result = traiterSoumissionFormulaire($bdd); 

if (is_string($result)) {
    $erreur = true;
    $messageErreur = $result;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>&copy; Waves</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="ecran">
        <div class="cote_gauche"> 
            <!-- image dans le css -->
        </div>
        <div class="cote_droite">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <section class="screen">
                    <h1><b>Se connecter</b></h1>
                    <div class="login_contenue">
                        <p>Vous n'êtes toujours pas inscrits ? 
                        <a href="signup.php"><strong>S'inscrire</strong></a></p>
                    </div>
                    <?php
                    if ($erreur) {
                        echo "<div class='erreur'>$messageErreur</div>";
                    }
                    ?>
                </section>
                <div class="input-conteneur mail">
                    <label for="email">Adresse Mail ou Identifiant</label>
                    <input id="email" name="email" placeholder ="Saisir votre email" type="email">
                </div>
                <div class="input-conteneur mdp">
                    <label for ="mdp">Mots de passe</label>
                    <input id="mdp" name="mot_de_passe" placeholder="Saisir votre mot de passe" type="password">
                </div>
                <div class="input-conteneur contrat">
                    <label class="check-contrat">
                        <input type="checkbox" id="remember-password" name="remember-password">
                        <span class="check"></span>
                        Enregistrer mon mot de passe pour la prochaine fois.
                    </label>
                </div>
                <button class="bouton-envoyer" type="submit">Se connecter</button>
                <section class="screen legal">
                    <p><span class="small">En continant, vous accepter nos <br> <a href="#">Politique de confidentialité</a> &amp; <a href="#">Conditions et services</a>.</span></p>
                </section>
            </form> 
        </div>
    </div>
</body>
</html>