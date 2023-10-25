<?php
require_once ("bdd.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
$title = "Reset your password";
$partie = 1;

@$email = htmlspecialchars($_POST["mail"]);
//Notre code ici


if(isset($_POST["SIGNUP"])){
    //Le formulaire est soumis

    // Vérifions si le champ email est rempli
    if(!empty($_POST["mail"])){
        //Le champ email est bien rempli

        //On va vérifier si l'adresse est valide
        if(filter_var(htmlspecialchars($_POST["mail"]), FILTER_VALIDATE_EMAIL)){
            //L'adresse entrée est valide
            $email = htmlspecialchars($_POST["mail"]);
            //On ira dans la base de données pour vérifier si l'adresse est enrégistrée 
            $requete = $pdo->prepare("SELECT * FROM users WHERE mail = :mail");
            $requete->bindParam(':mail', $email);
            $requete->execute();
            $requete = $requete->fetchAll(PDO::FETCH_ASSOC);
            if($requete){
                //l'adresse est dans la base de données

                //On va voir si le compte est confirmé
				$id = (int) $requete[0]['id'];
				$delai = (int)$requete[0]['delai'];
				$status = (int)$requete[0]['status'];
                $username = $requete[0]['username'];

				if($status === 1){
                    //Compte confirmé
					//On va renitialisé le password
                    



                    $code =rand(15000,100000000);
					$status = 0;
					$delai = time()+60*5;
					$requete = $pdo->prepare("UPDATE users SET code = :code, delai = :delai, status = :status WHERE id = :id");
					$requete->bindParam(':status',$status);
					$requete->bindParam(':code',$code);
					$requete->bindParam(':delai',$delai);
                    $requete->bindParam(':id',$id);
					$requete->execute();

                    //On va lui envoyer le code par mail


                    
                    $mail = new PHPMailer(true);

						try {
							//Server settings
							// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
							$mail->isSMTP();                                            //Send using SMTP
							$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
							$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
							$mail->Username   = 'youeemail@gmail.com';                     //SMTP username
							$mail->Password   = 'password';                               //SMTP password
                            $mail->SMTPDebug  = 0;  
							$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
							$mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
							//Recipients
							$mail->setFrom('avohouprince@gmail.com', 'AVOHOU Prince');
							$mail->addAddress($email,$username);     //Add a recipient
							// $mail->addAddress('ellen@example.com');               //Name is optional
							$mail->addReplyTo('avohouprince@gmail.com', 'AVOHOU Prince');
							// $mail->addCC('cc@example.com');
							// $mail->addBCC('bcc@example.com');
							// //Attachments
							// $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
							// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
							//Content
							$mail->isHTML(true);                                  //Set email format to HTML
							$mail->Subject = 'Inscription sur le site de AVOHOU';
							$mail->Body    = "Votre code de confirmation est<b> $code </b><br>Ce code est valide pour 5mn";
							$mail->AltBody = "Votre code de confirmation est $code <br>Ce code est valide pour 5mn";
							$mail->send();
						} catch (Exception $e) {
							// echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
							$erreur = "Erreur lors de l'envoi du code";
                            $partie = 1;
						}


















                    $partie = 2;
                    //Ensuite le redirect vers validercodepwd.php

				}else{
                    //Compte non confirmé 
                    //On va lui dire de confirmer son code
                    $erreur = "Votre compte n'est pas actif<a href=\"confirmer.php?mail=$email\">Confimer maintenant</a>";
                }
            }else{
                //L'adresse n'est pas dans la base de données
                $erreur = "L'adresse entrée est inconnue";
            }
        }else{
            //L'adresse entrée n'est pas valide
            $erreur = "L'adresse entrée est inconnue";
        }
    }else{
        //Le champ email est vide
        $erreur = "Veuillez entrer votre adresse mail.";
    }
}
if(isset($_POST["valider"])){
    $partie = 2;
    if(empty($_POST["code"])){

    }else{
        //Le code est rempli
        $requete = $pdo->prepare("SELECT * FROM users WHERE mail = :mail");
        $requete->bindParam(':mail', $email);
        $requete->execute();
        $requete = $requete->fetchAll(PDO::FETCH_ASSOC);
        $id = (int) $requete[0]['id'];
		$delai = (int)$requete[0]['delai'];
        $input = (int)$requete[0]['code'];
        $code = (int)$_POST['code'];
        if(time() > $delai){
            //delai expired
            header("Location:confirmer.php?mail={$email}&code=1");
        }else{
            if($code === $input){
                $requete = $pdo->prepare("UPDATE users SET  status = :status WHERE id = :id");
                $requete->bindParam(':id',$id);
                $status = 1;
                $requete->bindParam(':status',$status);
                $requete->execute();
                $requete->closeCursor();
                $partie = 3;
            }else{
                $erreur = "Code incorrect";
            }
        }
    }
}





if(isset($_POST["soumis"])){
    $partie = 3;
    $password = htmlspecialchars($_POST["password"]);
    if(empty($_POST["password"]) || empty($_POST["password1"])){
        //Tous les champs ne sont pas remplis
        $erreur = "Veuillez remplir tous les champs avant de soumettre ";
    }else{
        //Tous les champs sont remplis
        //On va vérifier l'uniformité des mots de passe
        if($password === htmlspecialchars($_POST["password1"])){
            //Les mots de passe sont identiques 
            if (preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!.@$%^&*-]).{8,}$/', $password)){
                //Le mot de passe est un mot de passe fort
                $password =password_hash($password, PASSWORD_DEFAULT,["cost"=>"12"]);
                $requete = $pdo->prepare("UPDATE users SET password = :password WHERE mail = :mail");
                $requete->bindParam(":mail",$email);
                $requete->bindParam(":password",$password);
                $requete->execute();
                $partie = 1;
                $succes = "Mot de passe modifié avec succès";
                header("location:connexion.php");
            }else{
                //Le mot de passe n'est pas un mot de passe fort
                $erreur = "Le mot de passe n'est pas un mot de passe fort";
            }
        }else{
            //Les mots de passe ne sont pas identiques
            $erreur = "Les mots de passe ne sont pas identiques";
        }
    }
}




 require_once("header.php");
?>
<?php if($partie == 1) : ?>
				<form action="resetpassword.php" method="post">
					<input class="text email" type="email" name="mail" placeholder="Email" autocomplete="off" required="required" value="<?php if($erreur) { echo $email;}else{echo "";} ?>">
					<?php require_once ("anim.php"); ?>
					<input type="submit" value="Valider" name="SIGNUP">
				</form>
<?php elseif($partie == 2) : ?>

				<form action="resetpassword.php" method="post">
					<input class="text" type="text" name="code" placeholder="Entrez votre code ici" autocomplete="off" required><br>
					<?php require_once ("anim.php"); ?>
					<input type="submit" value="Vérifier" name="valider">
                    <input type="hidden" value="<?= $email?>" name="mail">
				</form>
<?php else: ?>
                <form action="resetpassword.php" method="post">
					<input class="text" type="password" name="password" placeholder="Password" autocomplete="off" required="required" value="<?php if($erreur) { echo $password;}else{echo "";} ?>">
					<input class="text w3lpass" type="password" name="password1" placeholder="Confirm Password" autocomplete="off" required="required" value="<?php if($erreur) { echo htmlspecialchars($_POST['password1']);}else{echo "";} ?>">
					<?php require_once ("anim.php"); ?>
                     <input type="hidden" value="<?= $email?>" name="mail">
					<input type="submit" value="Vérifier" name="soumis">
				</form>
<?php endif ;?>
                <p><a href="inscription.php">S'inscrire</a></p><br>
				<p>Vous avez un compte? <a href="connexion.php"> Connectez-vous!</a></p>

<?php require_once("footer.php"); ?>