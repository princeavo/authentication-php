<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "bdd.php";

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

	// var_dump($_POST);
	// var_dump($_GET);

	$title = "Confirmer son compte";
	if(!isset($_GET['mail'])){
		header('location:inscription.php');
		die();
	}
	$email = htmlspecialchars($_GET['mail']);
	if(isset($_GET["mail"]) && isset($_GET["code"]) && $_GET["code"] ==1){
		//On veut un autre code de confirmation
		
		//On ira vérifier dans la base de données si l'adresse existe
		$email = htmlspecialchars($_GET['mail']);
		$requete = $pdo->prepare("SELECT * FROM users WHERE mail = :mail");
		$requete->bindParam(':mail',$email);
		$requete->execute();
		$requete = $requete->fetchAll(PDO::FETCH_ASSOC);
		if($requete){
			$id = (int) $requete[0]['id'];
			$delai = (int)$requete[0]['delai'];
			$status = (int)$requete[0]['status'];
			$username = $requete[0]['username'];
			if($status ==1){
				$erreur = "Votre compte est déjà actif";
			}else{
				if($delai<=time()){
					//Son code est déjà expiré
					$code =rand(15000,100000000);
					$delai = time()+60*5;
					$requete = $pdo->prepare("UPDATE users SET code = :code , delai = :delai WHERE id = :id");
					$requete->bindParam(':code',$code);
					$requete->bindParam(':delai',$delai);
					$requete->bindParam(':id',$id);

					$requete->execute();
					//On envoie le mail



					$mail = new PHPMailer(true);

						try {
							//Server settings
							// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
							$mail->SMTPDebug  = 0;  
							$mail->isSMTP();                                            //Send using SMTP
							$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
							$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
							$mail->Username   = 'youremail@here';                     //SMTP username
							$mail->Password   = 'yourpasswordhere';                               //SMTP password
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
							$succes = "Le code est envoyé par mail";
							unset($_POST);
							header('location:confirmer.php?mail='.$email);
						} catch (Exception $e) {
							// echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
							$erreur = "Erreur lors de l'envoi du code";
						}



				}
				
				header("Location:confirmer.php?mail=$email");
			}
		}else{
			// header("Location:inscription.php");
		}
			
	}
	if (isset($_POST['valider'])){
		if(!isset($_GET['mail'])){
			header('location:inscription.php');
			die();
		}else{
			$email = htmlspecialchars($_GET['mail']);
			$requete = $pdo->prepare("SELECT * FROM users WHERE mail = :mail");
			$requete->bindParam(':mail',$email);
			$requete->execute();
			$requete = $requete->fetchAll(PDO::FETCH_ASSOC);
			if($requete){
				$code = (int)$requete[0]['code'];
				$id = (int) $requete[0]['id'];
				$input = (int)htmlspecialchars($_POST["code"]);
				$delai = (int)$requete[0]['delai'];
				$status = (int)$requete[0]['status'];

				if($status === 1){
					header("location:inscription.php?mail=".$email);
				}else{
					if(time()<=$delai){
						if($code === $input){
							//Code conforme
							//On met le status à 1
							$requete =$pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
							$status = 1;
							$requete->bindParam(':status',$status);
							$requete->bindParam(':id',$id);
							$requete->execute();
							header("location:inscription.php?mail=".$email);
						}else{
							//Le code est incorrect
							$erreur="Le code est incorrect";
						}
					}else{
						//Delai expiré
						$erreur = "Votre code est déjà expiré <a href=\"confirmer.php?mail=$email&code=1\">Demander un autre code</a> ";
					}
				}

			}else{
				$erreur = "Adresse inconnue";
			}
			// var_dump($requete->fetchAll(PDO::FETCH_ASSOC));
		}
	}
	require_once ("header.php");
?>


				<form action="confirmer.php?mail=<?=$email?>" method="post">
					<input class="text" type="text" name="code" placeholder="Entrez votre code ici" autocomplete="off" required><br>
					<?php require_once ("anim.php"); ?>
					<input type="submit" value="Vérifier" name="valider">
				</form>
				<p><a href="valider.php">Valider son code de confirmation</a></p><br>
				<p>Vous avez un compte? <a href="connexion.php"> Connectez-vous!</a></p>
			<?php require_once("footer.php"); ?>