<?php
/* === connexion.php === */
require_once('inc/init.inc.php'); 


// Traitements pour la deconnexion
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion'){
	unset($_SESSION['membre']);
	header('location:connexion.php');
}


// Redirection si l'utilisateur est déjà connecté
if(userConnecte()){
	header('location:profil.php');
}


// Traitements pour la connexion
if($_POST){
	//debug($_POST); 
	
	$resultat = $pdo -> prepare("SELECT * FROM membre WHERE pseudo = :pseudo");	
	$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
	$resultat -> execute(); 
	
	if($resultat -> rowCount() > 0){ // Si $resultat -> rowCount() est supérieur à 0 cela signifie que l'utilisateur existe bien dans la BDD
		
		// Je compare le MDP en POST et le MDP en BDD. Si semblables alors tout est OK ! 
		$membre = $resultat -> fetch(PDO::FETCH_ASSOC);
		
		debug($membre);
		if(md5($_POST['mdp']) == $membre['mdp']){ // Les deux MDP sont semblables je peux connecter l'utilisateur
			// Je fais une boucle qui va récupérer toutes les infos du membre pour les enregistrer dans la SESSION. J'organise ma SESSION en array Multidimentionnel (membre, panier)
			
			foreach($membre as $indice => $valeur){
				if($indice != 'mdp'){
					$_SESSION['membre'][$indice] = $valeur;
				}
			}
			
	
			//redirection vers le profil.
			header('location:profil.php');
		}
		else{
			$msg .= '<div class="erreur">Erreur de Mot de passe !</div>';
		}
	}
	else{
		$resultat = $pdo -> prepare("SELECT * FROM membre WHERE email = :email");	
		$resultat -> bindParam(':email', $_POST['pseudo'], PDO::PARAM_STR);
		$resultat -> execute(); 
	
		if($resultat -> rowCount() > 0){
			$membre = $resultat -> fetch(PDO::FETCH_ASSOC);
		
			debug($membre);
			if(md5($_POST['mdp']) == $membre['mdp']){ // Les deux MDP sont semblables je peux connecter l'utilisateur
			// Je fais une boucle qui va récupérer toutes les infos du membre pour les enregistrer dans la SESSION. J'organise ma SESSION en array Multidimentionnel (membre, panier)
			
				foreach($membre as $indice => $valeur){
					if($indice != 'mdp'){
						$_SESSION['membre'][$indice] = $valeur;
					}
				}
				
		
				//redirection vers le profil.
				header('location:profil.php');
				}
			else{
				$msg .= '<div class="erreur">Erreur de Mot de passe !</div>';
			}
		}
		$msg .= '<div class="erreur">Erreur de Login !</div>';
	}
}

$page = 'Connexion';
require_once('inc/header.inc.php');
?>
<h1>Connexion</h1>
<form method="post" action="">
	<?= $msg ?>
	<label>Pseudo ou Email :</label>
	<input type="text" name="pseudo"/><br/><br/>
	
	<label>Mot de passe :</label>
	<input type="password" name="mdp"/><br/>
	
	<input type="submit" value="Connexion" />
</form>
<?php
require_once('inc/footer.inc.php');
?>