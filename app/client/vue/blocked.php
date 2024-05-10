<?php include "header.php"; ?>
<div class="feed">
    <div class="feed__header">
        <h2><?php $formatNomPrenom = '@' . strtolower($prenom . '_' . $nom);
        echo ' ' . $formatNomPrenom;?></h2>
    </div>  
    <div class="container">
        <div class="blocked-message">
            <h1>Accès Restreint</h1>
            <p>Nous sommes désolés, mais votre compte a été bloqué. Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administration.</p>
            <a href="contact.php">Contacter l'Administration </a>
        </div>
    </div>   
    
</div>
<?php include "header_end.php"; ?>