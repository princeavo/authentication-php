<?php
require_once "bdd.php";


$title = "Valider son compte";

if(isset($_POST['SIGNUP'])){
    //Le formulaire est envoyé
    $email = htmlspecialchars($_POST['mail']);
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        // Le mail est valide
		$requete = $pdo->prepare("SELECT * FROM users WHERE mail = :mail");
		$requete->bindParam(':mail',$email);
		$requete->execute();
		$requete = $requete->fetchAll(PDO::FETCH_ASSOC);
		if($requete){
            //L'adresse est dans la table
            $code = (int)$requete[0]['code'];
			$id = (int) $requete[0]['id'];
			$status = (int)$requete[0]['status'];
            if($status ===1){
                //L'adresse est déjà confirmée
                $succes = "Le mail est déjà confirmé";
            }else{
                header("location:confirmer.php?mail=".$email);
            }
        }else{
            $erreur = "L'adresse est inconnue";
        }
    }else{
        //Le mail est invalide
        $erreur = "L'adresse est inconnue.";
    }
}
require_once("header.php");
?>
				<form action="valider.php" method="post">
					<input class="text email" type="email" name="mail" placeholder="Email" autocomplete="off" required="required" value="<?php if($erreur) { echo $email;}else{echo "";} ?>">
					<?php require_once ("anim.php"); ?>
					<input type="submit" value="Valider" name="SIGNUP">
				</form>
				<p><a href="inscription.php">S'inscrire</a></p><br>
				<p>Vous avez un compte? <a href="connexion.php"> Connectez-vous!</a></p>
<?php require_once("footer.php"); ?>