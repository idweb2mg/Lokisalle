<?php
require_once('../inc/init.inc.php');

// redirection si pas admin
if(!userAdmin()){
	header('location:../connexion.php');
}



// Ajouter et modifier un produit
// Dans ces traitements on va à la fois être capable d'ajouter un nouveau produit, et à la fois modifier un produit existant. 
if($_POST){
	
	// debug($_POST);
	// debug($_FILES);
	//$_FILES est une supergloble (array multidimentionnel) qui récupère les infos des fichiers uploadés. Pour chaque fichier on récupère le nom, le type, l'emplacement temporaire, erreur (BOOL), la taille en octets. 
	
	//$nom_photo va quoi qu'il arrive contenir le nom de la photo a enregistrer en BDD.
	//Elle va soit contenir un nom par défaut, soit le nom de l'image uploadé (on modifiera le nom) soit le nom de la photo du produit en cours de modification. 
	// Dans le cas où une nouvelle photo est ajoutée, en plus de renommer cette photo (pour éviter les collision) je l'enregistre dans le serveur (fonction copy())
	
	$nom_photo = 'default.jpg'; 
	
	if(isset($_POST['photo_actuelle'])){
		$nom_photo = $_POST['photo_actuelle'];
	}
	
	if(!empty($_FILES['photo']['name'])){
		// On renomme la photo (pour éviter les doublons sur notre serveur)
		$nom_photo = $_POST['reference'] . '_' . $_FILES['photo']['name'];
		
		//Enregistre la photo sur le serveur. 
		$chemin_photo = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . 'photo/' . $nom_photo;
		//$chemin_photo est l'emplacement définitif de la photo depuis la base du serveur jusqu'au nom du fichier. 
		
		
		copy($_FILES['photo']['tmp_name'], $chemin_photo); // On déplace la photo depuis son emplacement temporaire, vers son emplacement définitif. Emplacement temporaire : $_FILES['photo']['tmp_name']
	}
	
	// Enregistrement dans la BDD : 
	// Depuis SQL 5.7, dans une requête REPLACE on ne peux plus mettre la clé primaire vide ou NULL. ON doit donc faire une requête pour l'ajout et une requete pour la modif. d'où le if/else ci-desous. 
	
	if(isset($_GET['action']) && $_GET['action'] == 'modifier'){
		$resultat = $pdo -> prepare("REPLACE INTO produit (id_produit, reference, categorie, titre, description, couleur, taille, public, photo, prix, stock) VALUES (:id_produit, :reference, :categorie, :titre, :description, :couleur, :taille, :public, '$nom_photo' , :prix, :stock)");
		
		$resultat -> bindParam(':id_produit', $_POST['id_produit'], PDO::PARAM_INT);
	}
	else{
		$resultat = $pdo -> prepare("INSERT INTO produit (reference, categorie, titre, description, couleur, taille, public, photo, prix, stock) VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :public, '$nom_photo' , :prix, :stock)");
	}// !!!!!!! FERMETURE DU ELSE !!!!!!!! 	
	
	//STR
	$resultat -> bindParam(':reference', $_POST['reference'], PDO::PARAM_STR);
	$resultat -> bindParam(':categorie', $_POST['categorie'], PDO::PARAM_STR);
	$resultat -> bindParam(':titre', $_POST['titre'], PDO::PARAM_STR);
	$resultat -> bindParam(':description', $_POST['description'], PDO::PARAM_STR);
	$resultat -> bindParam(':couleur', $_POST['couleur'], PDO::PARAM_STR);
	$resultat -> bindParam(':taille', $_POST['taille'], PDO::PARAM_STR);
	$resultat -> bindParam(':public', $_POST['public'], PDO::PARAM_STR);
	
	//INT
	$resultat -> bindParam(':prix', $_POST['prix'], PDO::PARAM_INT);
	$resultat -> bindParam(':stock', $_POST['stock'], PDO::PARAM_INT);
	
	if($resultat -> execute()){
		$_GET['action'] = 'affichage';
		$last_id = $pdo -> lastInsertId();
		$msg .= '<div class="validation">Le produit N°' . $last_id . ' a bien été enregistré</div>'; 
	}
	// Pourquoi effectuer -> execute() dans le if ? 
	// Après avoir executer ma requête, je souhaite lancer d'autres traitements (affichage d'un message, redirection etc...). le problème est que ces traitements ce lanceront quoi qu'il arrive, même si la requête echoue. 
	// en effectuant ces traitements dans un if($resultat -> execute()) cela garantit qu'ils ne s'effectueront qu'en cas de succès de la requête.
	// ====> Si la requete echoue on fait rien !! 
	
}


// Supprimer un produit
// Il faut d'abord supprimer du serveur la photo correspondant au produit pour faire les choses "proprement". 
if(isset($_GET['action']) && $_GET['action'] == 'supprimer'){ // SI une action de supprimer est passée dans l'url, on vérifie qu'il y a bien un ID et que cette ID est une valeur numérique. 
	if(isset($_GET['id_produit']) && is_numeric($_GET['id_produit'])){
		//Si c'est OK au niveau de l'ID, puis que je dois supprimer la photo du produit je dois récupérer le nom de la photo dans la BDD. D'où la requete de selection ci-dessous :
		$resultat = $pdo -> prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
		$resultat -> bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
		$resultat -> execute();
		
		if($resultat -> rowCount() > 0){
			// Si on a trouvé au moins un produit existant dans la BDD, c'est que l'ID était bien correcte. On vérifie cela au cas où l'ID transmis dans l'URL aurait été modifié ou erroné...
			$produit = $resultat -> fetch(PDO::FETCH_ASSOC);
	
			// Pour pouvoir supprimer une photo, il nous faut son chemin absolu, que l'on reconstitue depuis la racine du serveur ci-dessous:
			$chemin_de_la_photo_a_supprimer = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . 'photo/' . $produit['photo'];
		
			// Dernieres vérifs : Si le fichier existe et que ce n'est pas la photo par défault, alors la fonction unlink() supprime le fichier.
			if(file_exists($chemin_de_la_photo_a_supprimer) && $produit['photo'] != 'default.jpg'){
				unlink($chemin_de_la_photo_a_supprimer); // unlink : supprime un fichier de mon serveur. 
			}
			
			//Après avoir supprimer la photo du produit on peut enfin supprimer le produit lui-même de notre BDD : 
			$resultat = $pdo -> exec("DELETE FROM produit WHERE id_produit = $produit[id_produit]");
			
			if($resultat != FALSE){
				$_GET['action'] = 'affichage';
				$msg .= '<div class="validation">Le produit N°' . $produit['id_produit'] . ' a bien été supprimé !</div>';	
			}
		}
	}
}


// Récupérer toutes les infos de tous les produits
// Afficher toutes les infos de tous les produits
if(isset($_GET['action']) && $_GET['action'] == 'affichage'){ // Si une action pour afficher les produits est demandée dans l'URL
	// Alors on récupère toutes les infos de tous les produits
	$resultat = $pdo -> query("SELECT * FROM produit"); 
	
	// On affiche ces infos via des boucles, dans un tableau HTML (stocké dans une variable $contenu
	$contenu .= '<table border="1">';
	$contenu .= '<tr>';
	for($i = 0; $i < $resultat -> columnCount(); $i++){
		$meta = $resultat -> getColumnMeta($i);
		$contenu .= '<th>' . $meta['name'] . '</th>';
	}
	$contenu .= '<th colspan="2">Actions</th>';
	$contenu .= '</tr>';
	while($produits = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
		$contenu .= '<tr>'; 
		foreach($produits as $indice => $valeur){
			// Lorsqu'on parcourt un enregistrement on souhaite afficher la photo dans une balise IMG et non en texte. On fait donc une condition dans le foreach :
			if($indice == 'photo'){
				$contenu .= '<td><img src="' . RACINE_SITE . 'photo/' . $valeur . '" height="100"/></td>';
			}
			else{
				$contenu .= ' <td>' . $valeur . '</td>';
			}
		}
		// En face de chaque enregistrement on ajoute deux actions : Modifie et supprimer en GET et précisant l'ID de chaque enregistrement. 
		$contenu .= '<td><a href="?action=modifier&id_produit='. $produits['id_produit'] .'"><img src="' . RACINE_SITE . 'img/edit.png"/></a></td>';
		$contenu .= '<td><a href="?action=supprimer&id_produit='. $produits['id_produit'] .'"><img src="' . RACINE_SITE . 'img/delete.png"/></a></td>';
		$contenu .= '</tr>';
	}
	$contenu .= '</table>';
}

$page = 'Gestion Boutique';
require_once('../inc/header.inc.php');
?>
<!-- Contenu de la page -->

<h1>Gestion de la boutique</h1>
<ul> 
	<!-- Les deux liens ci-dessous (sous-menu) permettent de lancer 2 actions : Affichage de tous les produits et Affichage du formulaire d'ajout de produit. -->
	<li><a href="?action=affichage">Afficher les produits</a></li>
	<li><a href="?action=ajout">Ajouter un produit</a></li>
</ul><hr/>
<?= $msg ?>
<?= $contenu ?>

<!-- Affichage du formulaire (ajouter ou pour modifier) -->
<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modifier')) : 
// Si une action d'ajout ou de modification est demandée via l'URL, dans ce cas, on affiche le formulaire ci-dessous.
?>
<?php
if(isset($_GET['id_produit']) && is_numeric($_GET['id_produit'])){ // Dans le cas où l'action est de modifier un produit, alors j'ai un ID dans l'URL, qui va me permettre de récupérer toutes les infos du produits à modifier (requête ci-dessous) :
	
	$resultat = $pdo -> prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
	$resultat -> bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
	if($resultat -> execute()){
		$produit_actuel = $resultat -> fetch(PDO::FETCH_ASSOC);
		//$produit_actuel est un array qui contient toutes les infos du produit à modifier.
	}
}	
// Si produit actuel existe (je suis dans le cadre d'une modif), alors je stocke les valeurs du produit dans des variables (plus simples pour les afficher dans le champs) sinon je stocke une valeur vide.
// Les lignes ci-dessous servent simplement à éviter de mettre trop de PHP dans notre formulaire. 
$reference = (isset($produit_actuel)) ? $produit_actuel['reference'] : '';
$categorie = (isset($produit_actuel)) ? $produit_actuel['categorie'] : '';
$titre = (isset($produit_actuel)) ? $produit_actuel['titre'] : '';
$description = (isset($produit_actuel)) ? $produit_actuel['description'] : '';
$couleur = (isset($produit_actuel)) ? $produit_actuel['couleur'] : '';
$taille = (isset($produit_actuel)) ? $produit_actuel['taille'] : '';
$photo = (isset($produit_actuel)) ? $produit_actuel['photo'] : '';
$public = (isset($produit_actuel)) ? $produit_actuel['public'] : '';
$prix = (isset($produit_actuel)) ? $produit_actuel['prix'] : '';
$stock = (isset($produit_actuel)) ? $produit_actuel['stock'] : '';

$action = (isset($produit_actuel)) ? 'Modifier' : 'Ajouter';
$id_produit = (isset($produit_actuel)) ? $produit_actuel['id_produit'] : '';
?>


<h2><?= $action ?> un produit</h2>

<form method="post" action="" enctype="multipart/form-data">
<!-- L'attribut enctype permet de gérer les fichiers uploadés et de mes traiter grâce à la superglobale $_FILES -->	
	<input type="hidden" name="id_produit" value="<?= $id_produit ?>" />

	
	<label>Référence: </label>
	<input type="text" name="reference" value="<?= $reference ?>" /><br/>
	
	<label>Catégorie: </label>
	<input type="text" name="categorie" value="<?= $categorie ?>"/><br/>
	
	<label>Titre: </label>
	<input type="text" name="titre" value="<?= $titre ?>"/><br/>
	
	<label>Description: </label>
	<textarea name="description"><?= $description ?></textarea><br/>
	
	<label>Couleur: </label>
	<input type="text" name="couleur" value="<?= $couleur ?>"/><br/>
	
	<label>Taille: </label>
	<input type="text" name="taille" value="<?= $taille ?>"/><br/>
	
	<label>Public: </label>
	<select name="public">
		<option>-- Selectionnez --</option>
		<option <?= ($public == 'm') ? 'selected' : '' ?> value="m">Homme</option>
		<option <?= ($public == 'f') ? 'selected' : '' ?> value="f">Femme</option>
		<option <?= ($public == 'mixte') ? 'selected' : '' ?> value="mixte">Mixte</option>
	</select><br/>
	
	<?php if(isset($produit_actuel)) : ?>
	<input type="hidden" name="photo_actuelle" value="<?= $photo ?>" />
	<img src="<?= RACINE_SITE ?>photo/<?= $photo ?>" width="100" /><br/>
	<?php endif; ?>
	
	<label>Photo : </label>
	<input type="file" name="photo"/><br/>

	<label>Prix: </label>
	<input type="text" name="prix" value="<?= $prix ?>"/><br/>
	
	<label>Stock: </label>
	<input type="text" name="stock" value="<?= $stock ?>"/><br/>
	
	<input type="submit" value="<?= $action ?>"/><br/>
	
</form>
<?php endif;?>

<?php
require_once('../inc/footer.inc.php');
?>