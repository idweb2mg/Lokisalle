<?php
require_once('inc/init.inc.php');

//Redirection si pas connecté 
if(!userConnecte()){ // Si la fonction me retourne FALSE
	header('location:connexion.php');
}

// Traitement pour modifier les infos de l'utilisateur
if($_POST){
	
	if(!empty($_POST['mdp'])){
		$resultat = $pdo -> prepare("REPLACE INTO membre (id_membre, pseudo, mdp, nom, prenom, email, civilite, statut) VALUES (:id, :pseudo, :mdp, :nom, :prenom, :email, :civilite, 0)");
				
		//STR
		$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
		$mdp_crypte = md5($_POST['mdp']);
		// la fonction MD5() me permet de crypter une chaine de caractères selon le protocole MD5 (clé de hashage md5). C'est le plus simple, il en existe d'autres. 
		$resultat -> bindParam(':mdp', $mdp_crypte , PDO::PARAM_STR);
		$resultat -> bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
		$resultat -> bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
		$resultat -> bindParam(':email', $_POST['email'], PDO::PARAM_STR);
		$resultat -> bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR);
		

		//INT
		$resultat -> bindParam(':id', $_SESSION['membre']['id_membre'], PDO::PARAM_INT);	
		
		if($resultat -> execute()){
			$id_membre = $_SESSION['membre']['id_membre'];
			$resultat = $pdo -> query("SELECT * FROM membre WHERE id_membre = $id_membre");
			$membre = $resultat -> fetch(PDO::FETCH_ASSOC);
			$msg .= '<div class="validation">Vos informations sont à jour !</div>';
			foreach($membre as $indice => $valeur){
				if($indice != 'mdp'){
					$_SESSION['membre'][$indice] = $valeur;
				}
			}
		}
	
	}
	else{
		$msg .= '<div class="erreur">Veuillez renseigner un MDP</div>';
	}
}






//Pour afficher les infos
extract($_SESSION['membre']);




require_once('inc/header.inc.php');
?>

<h1>Modifier le profil</h1>
<form method="post" action="">
	<?= $msg ?>		
	<label>Pseudo :</label>
	<input type="text" name="pseudo" value="<?= $pseudo ?>" required/><br/>
	
	<label>Mot de passe :</label>
	<input type="password" name="mdp" required/><br/>
	
	<label>Nom :</label>
	<input type="text" name="nom" value="<?= $nom ?>" required/><br/>
	
	<label>Prénom :</label>
	<input type="text" name="prenom" value="<?= $prenom ?>" required/><br/>
	
	<label>Email :</label>
	<input type="email" name="email" value="<?= $email ?>" required/><br/>
	
	<label>Civilité :</label>
	<select name="civilite" required>
		<option value="">-- Selectionnez -- </option>
		<option value="m" <?= ($civilite == 'm') ? 'selected' : '' ?>>Homme</option>
		<option value="f" <?= ($civilite == 'f') ? 'selected' : '' ?>>Femme</option>
	</select><br/>
	
	<input type="submit" value="Modifier les infos" />
	
	
	
<?php
require_once('inc/footer.inc.php');
?>	