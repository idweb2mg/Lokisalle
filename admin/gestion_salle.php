<?php
/*
-------------------------------
  GESTION DES SALLES
-------------------------------
*/
require_once('../inc/init.inc.php') ;

// redirection si pas admin
if(!userAdmin()){
	header('location:../connexion.php') ;
}



// Ajouter et modifier une salle
// Dans ces traitements on va à la fois être capable d'ajouter un nouveau salle, et à la fois modifier une salle existant. 
if($_POST){
	
	// debug($_POST) ;
	// debug($_FILES) ;
	//$_FILES est une supergloble (array multidimentionnel) qui récupère les infos des fichiers uploadés. Pour chaque fichier on récupère le nom, le type, l'emplacement temporaire, erreur (BOOL), la taille en octets. 
	
	//$nom_photo va quoi qu'il arrive contenir le nom de la photo a enregistrer en BDD.
	//Elle va soit contenir un nom par défaut, soit le nom de l'image uploadé (on modifiera le nom) soit le nom de la photo de la salle en cours de modification. 
	// Dans le cas où une nouvelle photo est ajoutée, en plus de renommer cette photo (pour éviter les collision) je l'enregistre dans le serveur (fonction copy())
	
	$nom_photo = 'default.jpg' ; 
	
	if(isset($_POST['photo_actuelle'])){
		$nom_photo = $_POST['photo_actuelle'] ;
	}
	
	if(!empty($_FILES['url_photo']['name'])){
		// On renomme la photo (pour éviter les doublons sur notre serveur)
		$nom_photo = $_POST['url_photo'] . '_' . $_FILES['url_photo']['name'] ;
		
		//Enregistre la photo sur le serveur. 
		$chemin_photo = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . 'photo/' . $nom_photo ;
		//$chemin_photo est l'emplacement définitif de la photo depuis la base du serveur jusqu'au nom du fichier. 
		
		
		copy($_FILES['url_photo']['tmp_name'], $chemin_photo) ; // On déplace la photo depuis son emplacement temporaire, vers son emplacement définitif. Emplacement temporaire : $_FILES['photo']['tmp_name']
	}
	
	// Enregistrement dans la BDD : 
	// Depuis SQL 5.7, dans une requête REPLACE on ne peux plus mettre la clé primaire vide ou NULL. ON doit donc faire une requête pour l'ajout et une requete pour la modif. d'où le if/else ci-desous. 
//debug($_GET) ;
	if(isset($_GET['action']) && $_GET['action'] == 'modifier'){
		$req = "
		REPLACE INTO salle (
		  id_salle
		, categorie
		, reference
		, titre
		, description
		, url_photo
		, pays
		, ville
		, code_postal
		, adresse
		, capacite
		, superficie
		, prix
		) VALUES (
		  :id_salle
		, :categorie
		, :reference
		, :titre
		, :description
		, '$nom_photo'
		, :pays
		, :ville
		, :code_postal
		, :adresse
		, :capacite
		, :superficie
		, :prix
		)" ;

		$resultat = $pdo -> prepare($req) ;
		$resultat -> bindParam(':id_salle', $_POST['id_salle'], PDO::PARAM_INT) ;
	}
	else {
		$req = "
		INSERT INTO salle (
		  categorie
		, reference
		, titre
		, description
		, url_photo
		, pays
		, ville
		, code_postal
		, adresse
		, capacite
		, superficie
		, prix
		) VALUES (
		  :categorie
		, :reference
		, :titre
		, :description
		, '$nom_photo'
		, :pays
		, :ville
		, :code_postal
		, :adresse
		, :capacite
		, :superficie
		, :prix
		)" ;
		$resultat = $pdo -> prepare($req) ;

	} // FIN if(isset($_GET['action']) && $_GET['action'] == 'modifier')	
	
	//STR
	$resultat -> bindParam(':categorie', $_POST['categorie'], PDO::PARAM_STR) ;
	$resultat -> bindParam(':reference', $_POST['reference'], PDO::PARAM_STR) ;
	$resultat -> bindParam(':titre', $_POST['titre'], PDO::PARAM_STR) ;
	$resultat -> bindParam(':description', $_POST['description'], PDO::PARAM_STR) ;
	//$resultat -> bindParam('$nom_photo', $_POST['photo'], PDO::PARAM_STR) ;
	$resultat -> bindParam(':pays', $_POST['pays'], PDO::PARAM_STR) ;
	$resultat -> bindParam(':ville', $_POST['ville'], PDO::PARAM_STR) ;
	$resultat -> bindParam(':code_postal', $_POST['code_postal'], PDO::PARAM_STR) ;
	$resultat -> bindParam(':adresse', $_POST['adresse'], PDO::PARAM_STR) ;	
	//INT
	$resultat -> bindParam(':capacite', $_POST['capacite'], PDO::PARAM_INT) ;
	$resultat -> bindParam(':superficie', $_POST['superficie'], PDO::PARAM_INT) ;
	$resultat -> bindParam(':prix', $_POST['prix'], PDO::PARAM_INT) ;
//debug($resultat -> execute()) ;
	if($resultat -> execute()){
		$_GET['action'] = 'affichage' ;
		$last_id = $pdo -> lastInsertId() ;
		$msg .= '<div class="validation">La salle N°' . $last_id . ' a bien été enregistrée</div>' ; 
	}
	// Pourquoi effectuer -> execute() dans le if ? 
	// Après avoir executer ma requête, je souhaite lancer d'autres traitements (affichage d'un message, redirection etc...). le problème est que ces traitements ce lanceront quoi qu'il arrive, même si la requête echoue. 
	// en effectuant ces traitements dans un if($resultat -> execute()) cela garantit qu'ils ne s'effectueront qu'en cas de succès de la requête.
	// ====> Si la requete echoue on fait rien !! 
	
}


// Supprimer une salle
// Il faut d'abord supprimer du serveur la photo correspondant à la salle pour faire les choses "proprement". 
if(isset($_GET['action']) && $_GET['action'] == 'supprimer'){ // SI une action de supprimer est passée dans l'url, on vérifie qu'il y a bien un ID et que cette ID est une valeur numérique. 
	if(isset($_GET['id_salle']) && is_numeric($_GET['id_salle'])){
		//Si c'est OK au niveau de l'ID, puis que je dois supprimer la photo de la salle je dois récupérer le nom de la photo dans la BDD. D'où la requete de selection ci-dessous :
		$req = "
		SELECT * 
		FROM salle
		where id_salle = :id_salle
		" ;

		$resultat = $pdo -> prepare($req) ;
		$resultat -> bindParam(':id_salle', $_GET['id_salle'], PDO::PARAM_INT) ;
		$resultat -> execute() ;
		
		if($resultat -> rowCount() > 0){
			// Si on a trouvé au moins une salle existante dans la BDD, c'est que l'ID était bien correcte. On vérifie cela au cas où l'ID transmis dans l'URL aurait été modifié ou erroné...
			$salle = $resultat -> fetch(PDO::FETCH_ASSOC) ;
	
			// Pour pouvoir supprimer une photo, il nous faut son chemin absolu, que l'on reconstitue depuis la racine du serveur ci-dessous:
			$chemin_de_la_photo_a_supprimer = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . 'photo/' . $salle['photo'] ;
		
			// Dernieres vérifs : Si le fichier existe et que ce n'est pas la photo par défault, alors la fonction unlink() supprime le fichier.
			if(file_exists($chemin_de_la_photo_a_supprimer) && $salle['url_photo'] != 'default.jpg'){
				unlink($chemin_de_la_photo_a_supprimer) ; // unlink : supprime un fichier de mon serveur. 
			}
			
			//Après avoir supprimer la photo de la salle on peut enfin supprimer la salle elle-même de notre BDD : 
			$req = "
			DELETE
			FROM salle
			WHERE id_salle = $salle[id_salle]
			" ;
			$resultat = $pdo -> exec($req) ;
			
			if($resultat != FALSE){
				$_GET['action'] = 'affichage' ;
				$msg .= '<div class="validation">La salle N°' . $salle['id_salle'] . ' a bien été supprimée !</div>' ;	
			} // FIN if($resultat != FALSE)
		} // FIN if($resultat -> rowCount() > 0)
	} // FIN if(isset($_GET['id_salle']) && is_numeric($_GET['id_salle']))
} // FIN if(isset($_GET['action']) && $_GET['action'] == 'supprimer')


// Récupérer toutes les infos de tous les salles
// Afficher toutes les infos de tous les salles
if(isset($_GET['action']) && $_GET['action'] == 'affichage'){ // Si une action pour afficher les salles est demandée dans l'URL
	// Alors on récupère toutes les infos de tous les salles
	$req = "SELECT * FROM salle" ;
	$resultat = $pdo -> query($req) ; 
	
	// On affiche ces infos via des boucles, dans un tableau HTML (stocké dans une variable $contenu
	$contenu .= '<table border="1">' ;
	$contenu .= '<tr>' ;
	for($i = 0 ; $i < $resultat -> columnCount() ; $i++){
		$meta = $resultat -> getColumnMeta($i) ;
		$contenu .= '<th>' . $meta['name'] . '</th>' ;
	}
	$contenu .= '<th colspan="2">Actions</th>' ;
	$contenu .= '</tr>' ;

	//debug($resultat -> fetch(PDO::FETCH_ASSOC)) ;

	while($salles = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
		$contenu .= '<tr>' ; 
		foreach($salles as $indice => $valeur){
			// Lorsqu'on parcourt un enregistrement on souhaite afficher la photo dans une balise IMG et non en texte. On fait donc une condition dans le foreach :
			if($indice == 'url_photo'){
				$contenu .= '<td><img src="' . RACINE_SITE . 'photo/' . $valeur . '" height="100"/></td>' ;
			}
			else{
				$contenu .= ' <td>' . $valeur . '</td>' ;
			}
		}
		// En face de chaque enregistrement on ajoute deux actions : Modifie et supprimer en GET et précisant l'ID de chaque enregistrement. 
		$contenu .= '<td><a href="?action=modifier&id_salle='. $salles['id_salle'] .'"><img src="' . RACINE_SITE . 'img/edit.png"/></a></td>' ;
		$contenu .= '<td><a href="?action=supprimer&id_salle='. $salles['id_salle'] .'" onclick="return confirm(\'Voulez-vous supprimer la salle ' . $salles['titre'] . ' ?\');"><img src="' . RACINE_SITE . 'img/delete.png"/></a></td>' ;
		$contenu .= '</tr>' ;
	}
	$contenu .= '</table>' ;
}

$page = 'Gestion Salle' ;
require_once('../inc/header.inc.php') ;
?>
<!-- Contenu de la page -->

<h1>Gestion de la salle</h1>
<ul> 
	<!-- Les deux liens ci-dessous (sous-menu) permettent de lancer 2 actions : Affichage de tous les salles et Affichage du formulaire d'ajout de salle. -->
	<li><a href="?action=affichage">Afficher les salles</a></li>
	<li><a href="?action=ajout">Ajouter une salle</a></li>
</ul><hr/>
<?= $msg ?>
<?= $contenu ?>

<!-- Affichage du formulaire (ajouter ou pour modifier) -->
<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modifier')) : 
// Si une action d'ajout ou de modification est demandée via l'URL, dans ce cas, on affiche le formulaire ci-dessous.
?>
<?php
if(isset($_GET['id_salle']) && is_numeric($_GET['id_salle'])){ // Dans le cas où l'action est de modifier une salle, alors j'ai un ID dans l'URL, qui va me permettre de récupérer toutes les infos du salles à modifier (requête ci-dessous) :
	$req = "
	SELECT * 
	FROM salle 
	WHERE id_salle = :id_salle
	";
	$resultat = $pdo -> prepare($req) ;
	$resultat -> bindParam(':id_salle', $_GET['id_salle'], PDO::PARAM_INT) ;
	if($resultat -> execute()){
		$salle_actuelle = $resultat -> fetch(PDO::FETCH_ASSOC) ;
		//$salle_actuelle est un array qui contient toutes les infos du salle à modifier.
	}
}	
// Si salle actuelle existe (je suis dans le cadre d'une modif), alors je stocke les valeurs de la salle dans des variables (plus simples pour les afficher dans le champs) sinon je stocke une valeur vide.
// Les lignes ci-dessous servent simplement à éviter de mettre trop de PHP dans notre formulaire.
$id_salle = (isset($salle_actuelle)) ? $salle_actuelle['id_salle'] : '' ;
$categorie = (isset($salle_actuelle)) ? $salle_actuelle['categorie'] : '' ;
$reference = (isset($salle_actuelle)) ? $salle_actuelle['reference'] : '' ;
$titre = (isset($salle_actuelle)) ? $salle_actuelle['titre'] : '' ;
$description = (isset($salle_actuelle)) ? $salle_actuelle['description'] : '' ;
$url_photo = (isset($salle_actuelle)) ? $salle_actuelle['url_photo'] : '' ;
$pays = (isset($salle_actuelle)) ? $salle_actuelle['pays'] : '' ;
$ville = (isset($salle_actuelle)) ? $salle_actuelle['ville'] : '' ;
$code_postal = (isset($salle_actuelle)) ? $salle_actuelle['code_postal'] : '' ;
$adresse = (isset($salle_actuelle)) ? $salle_actuelle['adresse'] : '' ;
$capacite = (isset($salle_actuelle)) ? $salle_actuelle['capacite'] : '' ;
$superficie = (isset($salle_actuelle)) ? $salle_actuelle['superficie'] : '' ;
$prix = (isset($salle_actuelle)) ? $salle_actuelle['prix'] : '' ;

$action = (isset($salle_actuelle)) ? 'Modifier' : 'Ajouter' ;

?>


<h2><?= $action ?> une salle</h2>

<form method="post" action="" enctype="multipart/form-data">
<!-- L'attribut enctype permet de gérer les fichiers uploadés et de mes traiter grâce à la superglobale $_FILES -->	
	<input type="hidden" name="id_salle" value="<?= $id_salle ?>" />

	<label>Catégorie </label>
	<select name="categorie" required>
		<option value="">-- Sélectionner --</option>
		<option <?= ($categorie == 'r') ? 'selected' : '' ?> value="m">Réception</option>
		<option <?= ($categorie == 'f') ? 'selected' : '' ?> value="f">Formation</option>
		<option <?= ($categorie == 'b') ? 'selected' : '' ?> value="f">Bureau</option>
	</select><br/>

	<label>Référence </label>
	<input type="text" name="reference" value="<?= $reference ?>" required/><br/>

	<label>Titre </label>
	<input type="text" name="titre" value="<?= $titre ?>" required/><br/>
	
	<label>Description </label>
	<textarea name="description" required><?= $description ?></textarea><br/>

	<?php if(isset($salle_actuelle)) : ?>
	<input type="hidden" name="photo_actuelle" value="<?= $photo ?>"  required/>
	<img src="<?= RACINE_SITE ?>photo/<?= $photo ?>" width="100" /><br/>
	<?php endif ; ?>

	<label>Photo</label>
	<input type="file" name="url_photo" required/><br/>
	
	<div class="clear"></div>

	<label>Pays </label>
<!-- 	<input type="text" name="pays" value="<?= $pays ?>" required/><br/> -->
	<select name="pays" required>
		<option value="">-- Sélectionner --</option>
		<option value="allemagne">Allemagne</option>
		<option value="belgique">Belgique</option>
		<option value="espagne">Espagne</option>
		<option value="france">France</option>
		<option value="italie">Italie</option>
		<option value="paysBas">Pays-Bas</option>
		<option value="royaumeUni">Royaume-Uni</option>
		<option value="russie">Russie</option>
		<option value="suede">Suède</option>
		<option value="suisse">Suisse</option>
	</select><br/>
	
	<div class="clear"></div>
	
	<label>Ville </label>
	<input type="text" name="ville" value="<?= $ville ?>" required /><br/>
	
	<label>Code Postal </label>
	<input type="text" name="code_postal" id="code_postal" maxlength="5" pattern="[0-9]{5}" title="5 chiffres requis entre 0 et 9" value="<?= $code_postal ?>" required/>
	
	<label>Adresse </label>
	<textarea id="adresse" name="adresse" rows="5" cols="40"value="<?= $adresse ?>" required></textarea>
	
	<label>Capacité (nombre de participants)</label>
	<input type="text" name="capacite"  value="<?= $capacite ?>" required/><br/>
		
	<label>Superficie en m2</label>
	<input type="text" name="superficie"  value="<?= $superficie ?>" required/><br/>
	
	<label>Prix (en euros) </label>
	<input type="text" name="prix"  value="<?= $prix ?>" required/><br/>
	
	<input type="submit" value="<?= $action ?>" /><br/>
	
</form>
<?php endif ;?>

<?php
require_once('../inc/footer.inc.php') ;
?>