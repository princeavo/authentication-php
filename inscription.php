<?php

$title = "Inscription";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "bdd.php";

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


if ( isset ( $_POST[ 'SIGNUP' ] ) ) {
    if(!empty($_POST['username']) && !empty($_POST["password"]) && !empty($_POST["password1"]) && !empty($_POST["mail"])){
    	//Tous les champs sont remplis
    	$username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
		$email = htmlspecialchars($_POST['mail']);
    	if(htmlspecialchars($_POST['password1']) === $password){
            //Les mots de passe sont identiques

			if(filter_var($email, FILTER_VALIDATE_EMAIL)){
				// L'adresse e-mail est valide

				//Nous allons vérifier que l'email n'est pas préalablement enrégistré 
				$chose = $pdo->prepare("SELECT * FROM users WHERE mail=:mail");
				$chose->bindParam(':mail',$email);
				$chose->execute();
				$chose = $chose->fetchAll(PDO::FETCH_ASSOC);

				if($chose){
					// L'adresse mail est déjà enrégistré sur mon site 
					$erreur = "L'adresse mail est déjà enrégistré sur mon site.Veuillez utiliser une autre<br>";
				}else{
					// L'adresse mail n'est pas déjà enrégistré sur mon site 

					//Vérifions si le mot de passe est fort

					if (preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!.@$%^&*-]).{8,}$/', $password)) {   

						//Le mot de pase est un mot de passe fort

						// if(!preg_match("/[A-Z]/",$passwd)){
						// 	//Le mot de passe ne contient pas de caractère(s) majucules
						// 	$maj = "red";
						// }
						// if(!preg_match("/[a-z]/",$passwd)){
						// 	//Le mot de passe ne contient pas de caractère(s) minuscules
						// 	$min = "red";
						// }
						// if(!preg_match("/[0-9]/",$passwd)){
						// 	//Le mot de passe ne contient pas de caractère(s) chiffres
						// 	$chiffre = "red";
						// }
						// if(!preg_match("/[#?!.@$%^&*-]/",$passwd)){
						// 	//Le mot de passe ne contient pas de caractère(s) spécial(s)
						// 	$special = "red";
						// }
						// if(strlen($passwd)<8){
						// 	//Le mot de passe ne contient pas de caractère(s) majucules
						// 	$len = "red";
						// }
            


						//chiffrons le mot de passe
						$password =password_hash($password, PASSWORD_DEFAULT,["cost"=>"12"]);
						//On va insérer les données dans la base de données
						$code =rand(15000,100000000);
						$status = 0;
						$delai = time()+60*5;
						$requete = $pdo->prepare("INSERT INTO users (mail, username, password,code,delai,status) VALUES (:mail, :username, :password,:code,:delai,:status)");
						$requete->bindParam(':mail',$email);
						$requete->bindParam(':username',$username);
						$requete->bindParam(':password',$password);
						$requete->bindParam(':status',$status);
						$requete->bindParam(':code',$code);
						$requete->bindParam(':delai',$delai);
						$requete->execute();

						//Create an instance; passing `true` enables exceptions
						$mail = new PHPMailer(true);

						try {
							//Server settings
							// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
							$mail->isSMTP();                                            //Send using SMTP
							$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
							$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
							$mail->Username   = 'yourEmailHere';                     //SMTP username
							$mail->Password   = 'yourPasswordHere';                               //SMTP password
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
							unset($_POST);
							header('location:confirmer.php?mail='.$email);
						} catch (Exception $e) {
							// echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
							$erreur = "Erreur lors de l'envoi du code";
						}
        			}else{
						//le mot de passe n'est pas un mot de passe fort 
						$erreur .= "Inscrivez-vous avec un mot de passe fort<br>";
					}
				}
			}else{
					// L'adresse e-mail n'est pas valide 
					$erreur .= "L'adresse email n'est pas valide<br>";
			}

        }else {
            //Les mots de passe ne sont pas identiques
            $erreur .= "Les mots de passe ne sont pas identiques<br>";
        }
    }else{
        //Tous les champs ne sont pas remplis 
        $erreur .= "Tous les champs ne sont pas remplis<br>";
    }
}else if(!empty($_GET["mail"])){
	$mail = htmlspecialchars($_GET["mail"]);
	$requete = $pdo->prepare("SELECT * FROM users WHERE mail = :mail");
	$requete->bindParam(':mail',$mail);
	$requete->execute();
	$requete = $requete->fetchAll(PDO::FETCH_ASSOC);
	if($requete){
		$status = $requete[0]['status'];
		if($status==1){
			$succes = "Votre compte est bien enrégistré";
		}else{
			header("location:inscription.php");
		}
	}else{
		//L'adresse entrée n'est pas reconnue
		header("location:inscription.php");
	}
}
require_once ("header.php");
?>


				<form action="inscription.php" method="post">
					<input class="text" type="text" name="username" placeholder="Username" autocomplete="off" required="required" value="<?php if($erreur) { echo $username;}else{echo "";} ?>" >
					<input class="text email" type="email" name="mail" placeholder="Email" autocomplete="off" required="required" value="<?php if($erreur) { echo $email;}else{echo "";} ?>">
					<input class="text" type="password" name="password" placeholder="Password" autocomplete="off" required="required" value="<?php if($erreur) { echo $password;}else{echo "";} ?>">
					<input class="text w3lpass" type="password" name="password1" placeholder="Confirm Password" autocomplete="off" required="required" value="<?php if($erreur) { echo htmlspecialchars($_POST['password1']);}else{echo "";} ?>">
					<?php require_once ("anim.php"); ?>
					<input type="submit" value="SIGNUP" name="SIGNUP">
				</form>
				<p><a href="valider.php">Valider son code de confirmation</a></p><br>
				<p>Vous avez un compte? <a href="connexion.php"> Connectez-vous!</a></p>
<?php require_once("footer.php"); ?>