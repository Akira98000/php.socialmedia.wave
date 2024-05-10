<?php
require_once "../model/function.php";

verifierSession();

$nom = '';
$prenom = '';
$adr_mail = '';
$mdp = '';
$genre = '';
$ville = '';
$pays = '';
$datenaissance = '';
$bio = '';
$type_compte = '';
$messageErreur = '';
$est_admin = '';
$photo_profil = '';
$erreur = false; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    traitementSoumissionFormulaireSignUp($bdd);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>&copy; Waves</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="ecran">
        <div class="cote_gauche"></div>
        <div class="cote_droite">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" id="multi-step-form" >
                <div id="step-1">
                    <section class="screen">
                        <h1><b>S'inscrire à Waves</b></h1>
                        <div class="login_contenue">
                            <p>Êtes-vous déjà inscrit chez Waves ? <a href="login.php"><strong>Se connecter</strong></a></p>
                        </div>
                        <?php
                        if (isset($erreur) && $erreur) {
                            echo "<div class='erreur'>$messageErreur</div>";
                        }
                        ?>
                    </section>
                    <div class="input-conteneur nom">
                        <label for="nom">Nom de famille</label>
                        <input id="nom" name="nom" type="text" placeholder ="Choisir un nom" required>
                    </div>
                    <div class="input-conteneur prenom">
                        <label for="prenom">Prénom</label>
                        <input id="prenom" name="prenom" type="text" placeholder ="Choisir un prénom" required>
                    </div>
                    <div class="input-conteneur mail">
                        <label for="email">Adresse Mail</label>
                        <input id="email" name="email" type="email" placeholder ="Choisir un adresse Mail" required>
                    </div>
                    <div class="input-conteneur mdp">
                        <label for="mdp">Mot de passe</label>
                        <input id="mdp" name="mot_de_passe" placeholder="Minimum 8 caractères" type="password" required>
                    </div>
                    <button class="bouton-envoyer" type="button" onclick="nextStep(1)">Suivant</button>
                </div>
                <div id="step-2" style="display: none;">
                    <section class="screen">
                        <h1><b>Vos informations</b></h1>
                        <?php
                        if (isset($erreur) && $erreur) {
                            echo "<div class='erreur'>$messageErreur</div>";
                        }
                        ?>
                    </section>
                    <div class="input-conteneur nom">
                        <label for="date">Genre sexuel</label>
                        <select name="genre" required>
                            <option value="" disabled selected>à choisir</option>
                            <option value="Femme">Femme</option>
                            <option value="Homme">Homme</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div class="input-conteneur nom">
                        <label for="date">Date de naissance</label>
                        <input id="date" name="date" type="date" required>
                    </div>
                    <div class="input-conteneur prenom">
                        <label for="ville">Ville</label>
                        <input id="ville" name="ville" type="text" placeholder ="Choisir votre ville" required>
                    </div>
                    <div class="input-conteneur mail">
                        <label for="pays">Pays</label>
                        <input id="pays" name="pays" type="text" placeholder ="Choisir un pays" required>
                    </div>
                    <button class="bouton-envoyer" type="button" onclick="nextStep(2)">Suivant</button>
                </div>
                <div id="step-3" style="display: none;">
                    <section class="screen">
                        <h1><b>Personaliser votre compte</b></h1>
                        <?php
                        if (isset($erreur) && $erreur) {
                            echo "<div class='erreur'>$messageErreur</div>";
                        }
                        ?>
                    </section>
                    <div class="input-conteneur nom">
                        <label for="nom3">Biographie de votre compte</label>
                        <textarea name="bio" placeholder="Ecrire votre biographie"></textarea>
                    </div>
                    <div class="input-conteneur mail">
                        <label for="email3">Type de compte</label>
                        <select name="type_compte" required>
                            <option value="" selected>à choisir</option>
                            <option value="Particulier">Particulier</option>
                            <option value="Professionnel">Professionnel</option>
                        </select>
                    </div>
                    <div class="input-conteneur contrat">
                        <label class="check-contrat">
                            <input type="checkbox">
                            <input type="checkbox" name="est_admin" value="0" style="display: none;"> 
                            <span class="check"></span>
                            S'inscrire à la newsletter pour recevoir des notifications sur nos mises à jour.
                        </label>
                    </div>
                    <button class="bouton-envoyer" type="submit">Soumettre</button>
                    <section class="screen legal">
                        <p><span class="small">En continuant, vous acceptez nos <br> <a href="#">Politique de confidentialité</a> &amp; <a href="#">Conditions d'utilisation</a>.</span></p>
                    </section>
                </div>
            </form>
            <script>
                let currentStep = 1;

                function nextStep(step) {
                    document.getElementById(`step-${step}`).style.display = "none";
                    document.getElementById(`step-${step + 1}`).style.display = "block";
                    currentStep = step + 1;
                }

                function prevStep(step) {
                    document.getElementById(`step-${step}`).style.display = "none";
                    document.getElementById(`step-${step - 1}`).style.display = "block";
                    currentStep = step - 1;
                }

            </script>
        </div>
    </div>
</body>
</html>
