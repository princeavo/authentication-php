<?php 
	require_once "bdd.php";

	$title = "Confirmer le code de validation";

/*

	$mail = htmlspecialchars($_GET['mail']);
	if (isset($_POST['valider'])){
			$requete = $pdo->prepare("SELECT * FROM users WHERE mail = :mail");
			$requete->bindParam(':mail',$mail);
			$requete->execute();
			$requete = $requete->fetchAll(PDO::FETCH_ASSOC);
			if($requete){
				$code = (int)$requete[0]['code'];
				$id = (int) $requete[0]['id'];
				$input = (int)htmlspecialchars($_POST["code"]);
				$delai = (int)$requete[0]['delai'];
				$status = (int)$requete[0]['status'];

				if($status === 1){
					//Notre code ici
				}else{
                    $erreur = "Votre compte n'est pas actif";
                }

			}else{
				$erreur = "Adresse inconnue";
			}
	}
*/
	require_once ("header.php");
?>


				<form action="validercodepwd.php" method="post">
					<input class="text" type="password" name="password" placeholder="Password" autocomplete="off" required="required" value="<?php if($erreur) { echo $password;}else{echo "";} ?>">
					<input class="text w3lpass" type="password" name="password1" placeholder="Confirm Password" autocomplete="off" required="required" value="<?php if($erreur) { echo htmlspecialchars($_POST['password1']);}else{echo "";} ?>">
					<?php require_once ("anim.php"); ?>
					<input type="submit" value="Vérifier" name="valider">
				</form>
				<p>Don't have an Account? <a href="#"> Login Now!</a></p>
			<?php require_once("footer.php"); ?>