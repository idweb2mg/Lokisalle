<?php
require_once('inc/init.inc.php'); 

// Redirection si l'utilisateur est déjà connecté
if(userConnecte()){
	header('location:profil.php');
}

//TRAITEMENT DE L'INSCRIPTION
if($_POST){
	debug($_POST);
	
	// Vérifications des infos pour le pseudo : 
	$verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#' , $_POST['pseudo']); // preg_match() est une fonciton qui nous permet de vérifier les caractères d'une chaîne de caractères. Le 1er arg : les caractères autorisés (expression régulière ou REGEX) et le 2eme arg : la chaine de caractères qu'on vérifie). 
	// Cette fonction nous retourne soit TRUE soit FALSE. 
	
	if(!empty($_POST['pseudo'])){
		if($verif_caractere){
			if(strlen($_POST['pseudo']) < 3 || strlen($_POST['pseudo']) > 20){
				$msg .= '<div class="erreur">Veuillez renseigner un pseudo de 3 à 20 caractères ! </div>';	
			}
		}
		else{
			$msg .= '<div class="erreur">Pseudo : Caractères acceptés : A à Z, 0 à 9 et ".", "-" et "_" </div>';
		}
	}
	else{
		$msg .= '<div class="erreur">Veuillez renseigner un pseudo !</div>';
	}
	
	
	// Insertion du nouveau membre dans la BDD
	if(empty($msg)){ // Tout est OK, aucune erreur dans le formulaire si $msg est vide. 
		// Avant d'insérer le nx membre on doit vérifier si le pseudo est disponible. 
		$resultat = $pdo -> prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
		$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
		$resultat-> execute(); 
		
		if($resultat -> rowCount() > 0 ){ // Il y a au moins un résultat avec ce pseudo
			
			$msg .= '<div class="erreur">Ce pseudo ' . $_POST['pseudo'] . ' n\'est pas disponible, veuillez choisir un autre pseudo.</div>';
			
		}
		else{ // Tout est OK le pseudo est disponible on peut enregistrer le membre. Notons que nous aurions du vérifer la disponibilité de l'adresse email. En sachant que ce serait certainement une perte de MDP. 
		
		$resultat = $pdo -> prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, ville, code_postal, adresse, statut) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :ville, :code_postal, :adresse, 0)");
			
		//STR
		$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
		$mdp_crypte = md5($_POST['mdp']);
		// la fonction MD5() me permet de crypter une chaine de caractères selon le protocole MD5 (clé de hashage md5). C'est le plus simple, il en existe d'autres. 
		$resultat -> bindParam(':mdp', $mdp_crypte , PDO::PARAM_STR);
		$resultat -> bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
		$resultat -> bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
		$resultat -> bindParam(':email', $_POST['email'], PDO::PARAM_STR);
		$resultat -> bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR);
		$resultat -> bindParam(':ville', $_POST['ville'], PDO::PARAM_STR);
		$resultat -> bindParam(':adresse', $_POST['adresse'], PDO::PARAM_STR);

		//INT
		$resultat -> bindParam(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);	
			
		//$resultat -> execute();	
			
		// Redirection vers accueil ou vers connexion.php
		//header('location:connexion.php');
		
		if($resultat -> execute()){
			header('location:connexion.php');
		}
		
		
		// $msg .= '<div class="validation">L\'inscription est réussie !</div>';
			
		}
	}
}

$pseudo = (isset($_POST['pseudo'])) ? $_POST['pseudo'] : '';
$prenom = (isset($_POST['prenom'])) ? $_POST['prenom'] : '';
$nom = (isset($_POST['nom'])) ? $_POST['nom'] : '';
$email = (isset($_POST['email'])) ? $_POST['email'] : '';
$civilite = (isset($_POST['civilite'])) ? $_POST['civilite'] : '';
$ville = (isset($_POST['ville'])) ? $_POST['ville'] : '';
$adresse = (isset($_POST['adresse'])) ? $_POST['adresse'] : '';
$code_postal = (isset($_POST['code_postal'])) ? $_POST['code_postal'] : '';
// Ces lignes correspondent à des If() + Affectation de manière très contractée. C'est l'équivalent de : 
//if(isset($_POST['pseudo'])){$pseudo = $_POST['pseudo'];}else{$pseudo = '';}
	

$page="Inscription";
require_once('inc/header.inc.php');
?>
<h1>Inscription</h1>

<form method="post" action="">
	<?= $msg ?>	
	<label>Pseudo :</label>
	<input type="text" name="pseudo" value="<?= $pseudo ?>"/><br/>
	
	<label>Mot de passe :</label>
	<input type="password" name="mdp"/><br/>
	
	<label>Nom :</label>
	<input type="text" name="nom" value="<?= $nom ?>"/><br/>
	
	<label>Prénom :</label>
	<input type="text" name="prenom" value="<?= $prenom ?>"/><br/>
	
	<label>Email :</label>
	<input type="text" name="email" value="<?= $email ?>"/><br/>
	
	<label>Civilité :</label>
	<select name="civilite">
		<option>-- Selectionnez -- </option>
		<option value="m" <?= ($civilite == 'm') ? 'selected' : '' ?>>Homme</option>
		<option value="f" <?= ($civilite == 'f') ? 'selected' : '' ?>>Femme</option>
	</select><br/>
	
	<label>Ville :</label>
	<input type="text" name="ville" value="<?= $ville ?>"/><br/>
	
	<label>Code postal :</label>
	<input type="text" name="code_postal" value="<?= $code_postal ?>"/><br/>
	
	<label>Adresse :</label>
	<input type="text" name="adresse" value="<?= $adresse ?>"/><br/>
	
	<input type="submit" value="Inscription" />
</form>
<?php
require_once('inc/footer.inc.php');
?>