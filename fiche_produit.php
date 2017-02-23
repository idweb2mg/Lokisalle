<?php
require_once('inc/init.inc.php') ;

// Traitement pour récupérer toutes les infos du produit
if(isset($_GET['id_produit']) && $_GET['id_produit'] != ''){
	if(is_numeric($_GET['id_produit'])){
		$resultat = $pdo -> prepare("SELECT * FROM produit WHERE id_produit = :id_produit") ;
		$resultat -> bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_INT) ;
		$resultat -> execute() ; 
	
		if($resultat -> rowCount() > 0){
			// Si tout est OK, je récupère les infos du produit dans un array $produit
			$produit = $resultat -> fetch(PDO::FETCH_ASSOC) ; 
			// debug($produit) ;
			extract($produit) ;
			
			$resultat = $pdo -> query("SELECT ROUND(AVG(note), 0) as note_moyenne FROM note WHERE id_produit = $id_produit") ;
			$result = $resultat -> fetch(PDO::FETCH_ASSOC) ;
			extract($result) ;
			//$result['note_moyenne'] est un array avec les infos du produit en l'occurence la note moyenne 
			//$note_moyenne contient la note moyenne. 
			$resultat = $pdo -> query("SELECT * FROM note WHERE id_produit = $id_produit") ;
			$nbre_de_note = $resultat -> rowCount() ;
			// Nbre_de_note contient le nombre d'enregistrements que l'on a trouvés dans la table 'note' concernant ce produit. 
			// ON vérifie que tout marche bien : echo $note_moyenne . '-' . $nbre_de_note ; 
		}
		else{
			// Si l'ID ne correspond à aucun produit en BBD = REDIRECTION
			header('location:boutique.php') ; 
		}
	}
	else{
		// Si l'ID dans l'URL n'est pas un chiffre = REDIRECTION 
		header('location:boutique.php') ; 
	}
}
else{
	// S'il n'y a pas d'ID dans l'URL ou qu'il est vide = REDIRECTION
	header('location:boutique.php') ; 
}

//traitement pour ajouter le produit au panier
if($_POST && $_POST['quantite'] > 0){
	ajouterProduit($id_produit, $_POST['quantite'], $titre, $photo, $prix) ; 
}


// traitement pour récupérer toutes les suggestions de produit
$resultat = $pdo -> query("SELECT * FROM produit WHERE categorie != '$categorie' ORDER BY prix DESC LIMIT 0,5") ;
$suggestions = $resultat -> fetchAll(PDO::FETCH_ASSOC) ;



// TRAITEMENT POUR LES NOTATIONS

// Enregistrer la note
// On va afficher le bloc "Note" que si l'utilisateur est connecté
// On va afficher le bloc "Note" que si l'utilisateur n'a pas encore noté
// Récupérer la note moyenne du produit
// Afficher la note du produit


$note_valide = array('1', '2', '3', '4','5') ;
if(isset($_GET['note']) && !empty($_GET['note']) && in_array($_GET['note'], $note_valide)){ 
	// on vérifie que l'utilisateur est connecte et qu'il n'a pas déjà donné une note à ce produit.
	if(userConnecte()){
		$id_membre = $_SESSION['membre']['id_membre'] ;
		$resultat = $pdo -> query("SELECT * FROM note WHERE id_membre = $id_membre AND id_produit = $id_produit") ;
		
		if($resultat -> rowCount() == 0){
			$resultat = $pdo -> prepare("INSERT INTO note (id_membre, id_produit, note, date_enregistrement) VALUES ($id_membre, $id_produit, :note, NOW())") ;
			$resultat -> bindParam(':note', $_GET['note'], PDO::PARAM_STR) ;
			if($resultat -> execute()){
				header('location:fiche_produit.php?id_produit=' . $id_produit) ;
			}
		}
	}
}

if(userConnecte()){
	$id_membre = $_SESSION['membre']['id_membre'] ;
	$resultat = $pdo -> query("SELECT * FROM note WHERE id_membre = $id_membre AND id_produit = $id_produit") ;
	
	if($resultat -> rowCount() > 0){
		$note = $resultat -> fetch(PDO::FETCH_ASSOC) ;
		$note_user = $note['note'] ;
	}
}
 
require_once('inc/header.inc.php') ;
?>
<!-- Contenu HTML -->
<h1><?= $titre ?></h1>
<div>
	<p>Note (<?= $nbre_de_note ?> avis) : 
	<?php for($i=0 ; $i < 5 ; $i++) : ?>
		<?php if($i < $note_moyenne) : ?>
		<img src="img/star-2.png" width="15" />
		<?php else: ?>
		<img src="img/star.png" width="15" />	
		<?php endif ; ?>
	<?php endfor ; ?>
	</p>	
</div>
<img src="<?= RACINE_SITE ?>photo/<?= $photo ?>" width="250" /><br/>
<p>Détails du produit :</p>
<ul>
	<li>Référence : <b><?= $reference ?></b></li>
	<li>Catégorie : <b><?= $categorie ?></b></li>
	<li>Couleur : <b><?= $couleur ?></b></li>
	<li>Taille : <b><?= $taille ?></b></li>
	<li>Public : <b><?php if($public == 'm'){echo 'Homme' ;}elseif($public == 'f'){echo 'Femme' ;}else{echo 'Mixte (Homme et Femme' ;} ?></b></li>
	<li>Prix : <b style="color:red ; font-size: 20px ; font-weight: bold"><?= $prix ?>€</b></li>
</ul>
<br/>
<p>Description du produit :<br/>
<em><?= $description ?></em>
</p>
<br/>
<!-- Pour noter un produit -->
<fieldset>
	<legend>Notez ce produit<legend>
	<div class="notation">
		<?php if(userConnecte() && !isset($note_user)): ?>
		<!-- J'affiche la possibilité de noter que si l'utilisateur est connecté et qu'il n'a jamais noté ce produit -->
			<a class="star" href="?id_produit=<?= $id_produit?>&note=1" title="1/5"></a>
			<a class="star" href="?id_produit=<?= $id_produit?>&note=2" title="2/5"></a>
			<a class="star" href="?id_produit=<?= $id_produit?>&note=3" title="3/5"></a>
			<a class="star" href="?id_produit=<?= $id_produit?>&note=4" title="4/5"></a>
			<a class="star" href="?id_produit=<?= $id_produit?>&note=5" title="5/5"></a>
		<?php else : ?>
		<!-- Sinon cela signifie que soit il n'est pas connecté ou alors qu'il a déjà noté ce produit. Je traite donc les deux cas de figure : -->
			<?php if(!userConnecte()) : ?>
			<!-- S'il n'est pas connecté : -->
			<p>Pour noter ce produit veuillez vous <u><a href="connexion.php">connecter</a></u></p>
			
			<?php else : ?>
			<!-- S'il a déjà noté ce produit -->
			<p>Vous avez déjà noté ce produit (<?= $note_user ?>/5)</p>
			<?php endif ; ?>
		<?php endif ; ?>
		<?php endif ; ?>
	</div>
</fieldset>
<fieldset>
<legend>Acheter le produit :</legend>
<?php if($stock > 0): ?>
<em>Nombre de produit(s) en stock : <?= $stock ?></em>
<form method="post" action="">
	<select name="quantite" style="width: 40% ; float: left">
		<option>Quantité</option>
		<?php for($i=1  ; $i <= $stock && $i <= 5 ; $i++) : ?>
		<option><?= $i ?></option>
		<?php endfor ; ?>	
	</select>
	<input style="width: 40% ; float: left ; margin-left: 5%" type="submit" value="Ajouter au panier" />
</form>
<?php else : ?>
<em style="color : red">Rupture de stock !</em>
<?php endif ; ?>
</fieldset>

<!-- Si on veut ajouter des suggestions de produits -->
<fieldset>
	<legend>Suggestions de produits</legend>
	<?php foreach($suggestions as $valeur) : ?>
	<div class="boutique-produit" style="width:15% ; padding: 10px">
		<h3><?= $valeur['titre'] ?></h3>
		<a href="fiche_produit.php?id_produit=<?= $valeur['id_produit'] ?>"><img src="<?= RACINE_SITE ?>photo/<?= $valeur['photo'] ?>" height="100" /></a>
		<p style="font-weight: bold ; font-size:20px ;"><?= $valeur['prix'] ?>€</p>
		<p style="height: 40px"><?= substr($valeur['description'], 0, 40) ?>...</p><br/>
		<a style="padding: 5px 15px ; background: red ; color: white ; text-align: center ; border: 2px solid black ; border-radius: 3px" href="fiche_produit.php?id_produit=<?= $valeur['id_produit'] ?>">Voir la fiche</a>
	</div>
	<?php endforeach ; ?>
</fieldset>




<?php 
require_once('inc/footer.inc.php') ;
?>
