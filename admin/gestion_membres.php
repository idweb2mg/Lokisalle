<?php
require_once('../inc/init.inc.php');

// redirection si pas admin
if(!userAdmin()){
	header('location:../connexion.php');
}

// Ajouter et modifier un membre
if($_POST){
	
	//debug($_POST);

	if(isset($_POST['mdp']) && !empty($_POST['mdp'])){
			$contenu .= '<h2> Ajout et modification d\'un memebre</h2>';		
		
		if(isset($_GET['action']) && $_GET['action'] == 'modifier'){//modif
			$resultat = $pdo -> prepare("REPLACE INTO membre (id_membre, pseudo, mdp, nom, prenom, email, civilite, statut) VALUES (:id_membre, :pseudo, :mdp, :nom, :prenom, :email, :civilite, :statut)");
			
			$resultat -> bindParam(':id_membre', $_POST['id_membre'], PDO::PARAM_INT);
		}
		else{//ajout
			$resultat = $pdo -> prepare("REPLACE INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :statut)");
		}
		
		//STR
		$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
		$mdp = md5($_POST['mdp']);
		$resultat -> bindParam(':mdp', $mdp, PDO::PARAM_STR);
		$resultat -> bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
		$resultat -> bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
		$resultat -> bindParam(':email', $_POST['email'], PDO::PARAM_STR);
		$resultat -> bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR);
		$resultat -> bindParam(':statut', $_POST['statut'], PDO::PARAM_STR);
		
		if($resultat -> execute()){
			$_GET['action'] = 'affichage';
			$last_id = $pdo -> lastInsertId();
			$msg .= '<div class="validation">Le membre N°' . $last_id . ' a bien été enregistré</div>'; 
		}
	}
	else
	{
		$msg .= '<div class="erreur">Veuillez saisir un Mot de passe !</div>';
	}
}

// Supprimer un membre

if(isset($_GET['action']) && $_GET['action'] == 'supprimer'){ // SI une 
	if(isset($_GET['id_membre']) && is_numeric($_GET['id_membre'])){

		$resultat = $pdo -> prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
		$resultat -> bindParam(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);
		$resultat -> execute();
		
		if($resultat -> rowCount() > 0){
 
			$resultat = $pdo -> exec("DELETE FROM membre WHERE id_membre = $membre[id_membre]");
			
			if($resultat != FALSE){
				$_GET['action'] = 'affichage';
				$msg .= '<div class="validation">Le membre N°' . $membre['id_membre'] . ' a bien été supprimé !</div>';	
			}
		}
	}
}

// Récupérer toutes les infos de tous les membres
if(isset($_GET['action']) && $_GET['action'] == 'affichage'){ 

	$resultat = $pdo -> query("SELECT * FROM membre"); 
	
	$contenu .= '<h2>Liste des membres</h2>';
	$contenu .= '<table border="1">';
	$contenu .= '<tr>';
	for($i = 0; $i < $resultat -> columnCount(); $i++){
		$meta = $resultat -> getColumnMeta($i);
		if($meta['name'] != 'mdp'){
			$contenu .= '<th>' . $meta['name'] . '</th>';
		}
	}
	$contenu .= '<th colspan="2">Actions</th>';
	$contenu .= '</tr>';
	while($membres = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
		$contenu .= '<tr>'; 
		foreach($membres as $indice => $valeur){
			if($indice != 'mdp'){
				$contenu .= ' <td>' . $valeur . '</td>';
			}
		} 
		$contenu .= '<td><a href="?action=modifier&id_membre='. $membres['id_membre'] .'"><img src="' . RACINE_SITE . 'img/edit.png"/></a></td>';
		$contenu .= '<td><a href="?action=supprimer&id_membre='. $membres['id_membre'] .'"><img src="' . RACINE_SITE . 'img/delete.png"/></a></td>';
		$contenu .= '</tr>';
	}
	$contenu .= '</table>';
}

$page = 'Gestion Membres';
require_once('../inc/header.inc.php');
?>

<h1>Gestion de la boutique</h1>
<ul> 
	<li><a href="?action=affichage">Afficher les membres</a></li>
	<li><a href="?action=ajout">Ajouter un membre</a></li>
</ul><hr/>
<?= $msg ?>
<?= $contenu ?>
<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modifier')) : 
?>
<?php
if(isset($_GET['id_membre']) && is_numeric($_GET['id_membre'])){
	
	$resultat = $pdo -> prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
	$resultat -> bindParam(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);
	if($resultat -> execute()){
		$membre_actuel = $resultat -> fetch(PDO::FETCH_ASSOC);
	}
}	
$pseudo = (isset($membre_actuel)) ? $membre_actuel['pseudo'] : '';
$nom = (isset($membre_actuel)) ? $membre_actuel['nom'] : '';
$prenom = (isset($membre_actuel)) ? $membre_actuel['prenom'] : '';
$email = (isset($membre_actuel)) ? $membre_actuel['email'] : '';
$civilite = (isset($membre_actuel)) ? $membre_actuel['civilite'] : '';
$statut = (isset($membre_actuel)) ? $membre_actuel['statut'] : '';
$action = (isset($membre_actuel)) ? 'Modifier' : 'Ajouter';
$id_membre = (isset($membre_actuel)) ? $membre_actuel['id_membre'] : '';
?>


<h2><?= $action ?> un membre</h2>

<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="id_membre" value="<?= $id_membre ?>" required />

	
	<label>Pseudo : </label>
	<input type="text" name="pseudo" value="<?= $pseudo ?>"  required/><br/>
	
	<label>Mot de passe: </label>
	<input type="text" name="mdp" required/><br/>
	
	<label>Nom: </label>
	<input type="text" name="nom" value="<?= $nom ?>" required/><br/>
	
	<label>Prénom: </label>
	<input name="prenom" value="<?= $prenom ?>" required/><br/>
	
	<label>Email: </label>
	<input type="text" name="email" value="<?= $email ?>" required/><br/>
	
	<label>Civilite: </label>
	<select name="civilite" required>
		<option value="">-- Selectionnez --</option>
		<option <?= ($civilite == 'm') ? 'selected' : '' ?> value="m">Homme</option>
		<option <?= ($civilite == 'f') ? 'selected' : '' ?> value="f">Femme</option>
	</select><br/>
	
	<label>Statut: </label>
	<select name="statut" required>
		<option value="">-- Selectionnez --</option>
		<option <?= ($statut == '0') ? 'selected' : '' ?> value="0">Membre</option>
		<option <?= ($civilite == '1') ? 'selected' : '' ?> value="1">Admin</option>
	</select><br/>
	
	
	<input type="submit" value="<?= $action ?>"/><br/>
	
</form>
<?php endif;?>

<?php
require_once('../inc/footer.inc.php');
?>