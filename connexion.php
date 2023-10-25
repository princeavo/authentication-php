<?php
    $title = "Connectez-vous";
    require_once "bdd.php";
    //Traitement du formulaire
    if (isset($_POST["SIGNUP"])){
        $email = $_POST["mail"];
        $password = $_POST["password"];
        if(empty($email) || empty($password)){
            $erreur = "Veuillez remplir tous les champs avant de valider";
        }else{
            //Tous les champs sont remplis 
            
            //On va vérifier si l'adresse est valide
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                //L'adresse est valide
                $requete = $pdo->prepare("SELECT * FROM users WHERE mail = :mail");
                $requete->bindParam(':mail', $email);
                $requete->execute();
                $requete = $requete->fetchAll(PDO::FETCH_ASSOC);
                if($requete){
                    //On va vérifier le mot de passe 
                    $tpass = $requete[0]["password"]; // Le mot de passe haché
                    $status =(int) $requete[0]["status"];
                    if($status === 1){
                        if(password_verify($password,$tpass)){
                            //Informations correctes 
                            $succes = "Informations correctes";
                        }else{
                            //Mot de passe incorrects
                            $erreur = "Mot de passe incorrect <a href=\"resetpassword.php\" >Renitialiser son mot de passe</a>";
                        }
                    }else{
                        //Inscription non finalisée
                        $erreur = "Inscription non finalisée.<a href='valider.php'>Finaliser maintenant</a>";
                    }
                   
                }else{
                    // L'adresse n'est pas dans la table
                    $erreur = "L'adresse est inconnue";
                   
                }
            }else{
                //L'adresse n'est pas valide
                $erreur = "L'adresse est inconnue";
            }
        }
    }
       require_once "header.php";
?>
<form action="connexion.php" method="post">
	<input class="text email" type="email" name="mail" placeholder="Email" autocomplete="off" required="required" value="<?php if($erreur) { echo $email;}else{echo "";} ?>">
	<input class="text" type="password" name="password" placeholder="Password" autocomplete="off" required="required" value="<?php if($erreur) { echo $password;}else{echo "";} ?>">
	<br>
	<?php require_once ("anim.php"); ?>
	<input type="submit" value="Se connecter" name="SIGNUP">
</form>
<p><a href="valider.php">Valider son code de confirmation</a></p><br>
<p>Vous n'avez pas un compte? <a href="inscription.php"> Inscrivez-vous!</a></p>
<?php require_once("footer.php"); ?>