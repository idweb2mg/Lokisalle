<?php
require_once('inc/init.inc.php');


// traitement pour récupérer toutes les catégories
$resultat = $pdo -> query("SELECT DISTINCT categorie FROM produit"); 
$categorie = $resultat -> fetchAll(PDO::FETCH_ASSOC); 
// Grâce à fetchAll(), $categorie est un array multidimentionnel avec les infos de chaque categorie. A l'indice categorie, je trouve le nom de ma categorie. 

// debug($categorie);


// Traitement pour récupérer tous produits par catégorie (ou par default tous les produits du site)
if(isset($_GET['categorie']) && $_GET['categorie'] != ''){
	$resultat = $pdo -> prepare("SELECT * FROM produit WHERE categorie = :categorie");
	$resultat -> bindParam(':categorie', $_GET['categorie'], PDO::PARAM_STR);
	$resultat -> execute(); 
	
	if($resultat -> rowCount() > 0){
		$produits = $resultat -> fetchAll(PDO::FETCH_ASSOC);
	}
	else{
		$resultat = $pdo -> query("SELECT * FROM produit"); 
		$produits = $resultat -> fetchAll(PDO::FETCH_ASSOC);
		// Si on est dans ce ELSE cela signifie que notre requête n'a rien trouvé concernant cette catégorie... oupsss ! L'utilisateur a certainement modifié l'URL (cas exeptionnel entre l'arrivée sur cette page et le clic, on a plus de stock dans cette catégorie)
		// Dans ce cas, on peut soit recharger la page, soit rediriger vers une 404, soit effectuer une requête générique avec tous les produits
	}
}
else{
	$resultat = $pdo -> query("SELECT * FROM produit"); 
	$produits = $resultat -> fetchAll(PDO::FETCH_ASSOC);
	// On est dans ce ELSE, s'il n'y a pas de paramètre catégorie dans l'URL (quand on arrive sur cette page) ou alors si le paramètre catégorie est vide. 
}
// debug($produits);
// Qu'il y ait une catégorie dans l'URL ou pas je sors de cette condition avec $produit étant un array multidimentionnel avec les infos de plusieurs produits. 
$page="Boutique";
require_once('inc/header.inc.php');

?>
<h1>Boutique</h1>
<div class="boutique-gauche">
	<ul>
		<?php foreach($categorie as $valeur) : ?>
		<li><a href="?categorie=<?= $valeur['categorie'] ?>"><?= $valeur['categorie'] ?></a></li>
		<!-- href="boutique.php?categorie=nom_de_la_categorie" -->
		<?php endforeach; ?>
	</ul>
</div>
<div class="boutique-droite">
	
	<?php foreach($produits as $valeur) : ?>
	<div class="boutique-produit">
		<h3><?= $valeur['titre'] ?></h3>
		<a href="fiche_produit.php?id_produit=<?= $valeur['id_produit'] ?>"><img src="<?= RACINE_SITE ?>photo/<?= $valeur['photo'] ?>" height="100" /></a>
		<p style="font-weight: bold; font-size:20px;"><?= $valeur['prix'] ?>€</p>
		<p style="height: 40px"><?= substr($valeur['description'], 0, 40) ?>...</p><br/>
		<a style="padding: 5px 15px; background: red; color: white; text-align: center; border: 2px solid black; border-radius: 3px" href="fiche_produit.php?id_produit=<?= $valeur['id_produit'] ?>">Voir la fiche</a>
		<!-- fiche_produit.php?id_produit=54 -->
	</div>
	<?php endforeach; ?>
	
</div>







<?php
require_once('inc/footer.inc.php');
?>