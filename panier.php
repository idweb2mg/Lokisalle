<?php

require_once('inc/init.inc.php');


// TRAITEMENT POUR VIDER LE PANIER
if(isset($_GET['action']) && $_GET['action'] == 'vider'){
	unset($_SESSION['panier']);
}
// Si l'action de vider le panier est demandée dans l'URL alors on unset() la partie panier de la session. Si l'utilisateur était connecté, il reste connecté car la partie membre de SESSION existe toujours.



// TRAITEMENT POUR SUPPRIMER UN PRODUIT DU PANIER
if(isset($_GET['action']) && $_GET['action'] == 'supprimer'){
	if(isset($_GET['id_produit']) && !empty($_GET['id_produit']) && is_numeric($_GET['id_produit'])){	
		retirerProduit($_GET['id_produit']);
	}
} 
// Si une action de supprimer un produit du panier est demandée dans l'URL on vérifie que l'ID est bien présent, non vide et qu'il correspond bien à une valeur numérique. 
//Dans ce cas, on exécute une fonction retirerProduit() qui va supprimer le produit de SESSION['panier']


// TRAITEMENT POUR INCREMENTER UN PRODUIT
// Je peux incrémenter tant qu'il y a du stock. je dois donc aller chercher le stock dispo pour ce produit. 
if(isset($_GET['action']) && $_GET['action'] == 'incrementation' ){
	if(isset($_GET['id_produit']) && !empty($_GET['id_produit']) && is_numeric($_GET['id_produit'])){
		
		// S'il y a une action d'incrémentation demandée dans l'URL et que l'ID est correct (non vide, et numérique), on va chercher dans la BDD le stock disponible pour ce produit. 
		$resultat = $pdo -> prepare("SELECT stock FROM produit WHERE id_produit = :id_produit");
		$resultat -> bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
		$resultat -> execute(); 
		
		if($resultat -> rowCount() > 0){ // Si le produit existe bien dans la BDD, je peux comparer son stock avec le stock actuellement dans le panier, et ainsi ajouter une unité au panier si disponible. Pour ce faire il me faut l'emplacement du produit dans mon array panier, array_search() me permet de le trouver. 
			$produit = $resultat -> fetch(PDO::FETCH_ASSOC);
			$position = array_search($_GET['id_produit'], $_SESSION['panier']['id_produit']);
			if($position !== FALSE){
				if($produit['stock'] >= $_SESSION['panier']['quantite'][$position] +1){
					$_SESSION['panier']['quantite'][$position] ++;
					header('location:panier.php');
				}
				else{// Si le stock dispo n'est pas supérieur à la quantité actuelle dans le panier, plus une unité, on préviens que le stock est limité et donc on n'incrémente pas. 
					$msg .= '<div class="erreur">Le stock du produit ' .  $_SESSION['panier']['titre'][$position]  . '  est limité !</div>'; 
				}
			}			
		}
	}
}

// TRAITEMENT POUR LA DECREMENTATION
// Attention, on peut décrémenter la quantité d'un produit dans le panier tant que la quantité est supérieur à 0. Ensuite, il est préférable de supprimer entièrement la ligne. 
if(isset($_GET['action']) && $_GET['action'] == 'decrementation' ){
	if(isset($_GET['id_produit']) && !empty($_GET['id_produit']) && is_numeric($_GET['id_produit'])){

		// Pour agir sur la quantité du produit dans le panier, il nous faut son emplacement dans le panier. Pour ce faire, array_search() nous retourne sa position. 
		$position = array_search($_GET['id_produit'], $_SESSION['panier']['id_produit']);
	
		if($position !== FALSE){
			if($_SESSION['panier']['quantite'][$position] > 1){
				// Si le produit existe dans le panier, et que sa quantité est supérieure à 1, je peux retiré une unité
				$_SESSION['panier']['quantite'][$position] --; 
			}
			else{// Si sa quantité est inférieure à 1 dans ce cas, je supprime tout simplement la ligne. 
				retirerProduit($_GET['id_produit']);
				header('location:panier.php');
			}
		}
	}
}

// TRAITEMENT DU PAIEMENT : 
	// Vérifier que le stock est toujours dispo (boucle)
		// Si c'est non, deux cas de figure :
			// Stock inférieur à la demande : remplace la quantité
			// Le stock est nul : Retire le produit
	// Enregistre dans la BDD les infos de la commandes, pour chaque commandé on modifie le stock et on enregistre les infos dans détails commande. 
	// Envoyer un email de confirmation à nos client $_SESSION['membre']['email']
	
if(isset($_POST['paiement']) && !empty($_SESSION['panier']['id_produit'])){ // Si l'utilisateur à cliqué sur le bouton payer. 
	
	for($i = 0; $i < sizeof($_SESSION['panier']['id_produit']); $i ++){
		// Sizeof() et count() font la même chose
		$id_produit = $_SESSION['panier']['id_produit'][$i];
		$resultat = $pdo -> query("SELECT stock 
		FROM produit WHERE id_produit = $id_produit"); 
		
		$produit = $resultat -> fetch(PDO::FETCH_ASSOC);
		debug($produit);
		
		if($produit['stock'] < $_SESSION['panier']['quantite'][$i]){
			$msg .= '<div class="erreur">'. $_SESSION['panier']['titre'][$i] .' : Stock restant : ' . $produit['stock'] . '. Quantité demandée : '. $_SESSION['panier']['quantite'][$i] . '</div>';
			
			// 2 cas de figure : Stock nul ou simplement insufisant ? 
			if($produit['stock'] > 0){ // Stock insufisant ! 
				$msg .= '<div class="erreur">Le stock du produit ' . $_SESSION['panier']['titre'][$i] . ' n\'est pas suffisant, votre commande a été modifiée. Veuillez vérifier la nouvelle quantité avant de valider.</div>';
				
				$_SESSION['panier']['quantite'][$i] = $produit['stock'];
			}
			else{ // Stock Nul ! 
				$msg .= '<div class="erreur">Le produit ' . $_SESSION['panier']['titre'][$i] . ' n\'est plus disponible. Nous avons supprimé ce produit de votre commande. </div>';
				
				retirerProduit($_SESSION['panier']['id_produit'][$i]); 
				
				// attention !!!!!!!!!
				$i --;
				// Etant donné que $i parcourt toutes les lignes du panier. Lorsque je supprime une ligne, et que les suivantes remontent, $i risque d'en rater une. On doit donc OBLIGATOIREMENT le décrémenter afin de corriger l'erreur.
			}
		}
	}// fin du for

	if(empty($msg)){ // Si $msg est vide, cela signifie qu'il a pas de problème de stock on peut poursuivre le traitement pour le paiement. 
		// Enregistrement dans la BDD 
		// Envoyer un email
		// Suppression du panier
		
		// Enregistrement dans la table commande
		$id_membre = $_SESSION['membre']['id_membre'];
		$montant = montantTotal();
		
		$resultat = $pdo -> exec("INSERT INTO commande (id_membre, montant, date_enregistrement, etat) VALUES ('$id_membre', '$montant', NOW(), 'en cours de traitement')");
		
		$id_commande = $pdo -> lastInsertId();
		
		// Modification des stocks dans la table produit et enregistrement dans la table details_commande (boucle car opération a effectuer pour chaque produit du panier)
		
		for($i = 0; $i < sizeof($_SESSION['panier']['id_produit']); $i++){
			
			$id_produit = $_SESSION['panier']['id_produit'][$i];
			$quantite = $_SESSION['panier']['quantite'][$i];
			$prix = $_SESSION['panier']['prix'][$i];
			
			// enregistrement des details
			$resultat = $pdo -> exec("INSERT INTO details_commande (id_commande, id_produit, quantite, prix) VALUES ('$id_commande', '$id_produit', '$quantite', '$prix')"); 
			
			//modification du stock
			$resultat = $pdo -> exec("UPDATE produit set stock = (stock - $quantite )");	
		}
		unset($_SESSION['panier']);
		$msg .= '<div class="validation">Félicitations ! Votre numéro de commande est : ' . $id_commande . ' !</div>';
		//mail(); // Cf le fichier formulaire5.php dans POST. 
	}	
}





$page = 'Panier';
require_once('inc/header.inc.php');
?>
<!-- Contenu HTML -->
<h1>Panier</h1>
<?= $msg ?>
<table border="1" style="border-collapse: collapse; cellpadding:7;">
	<tr>
		<th colspan="6">PANIER <?= (quantitePanier()) ? quantitePanier() . ' Produit(s) dans le panier ' : '' ?></th>
	</tr>
	<tr>
		<th>Photo</th>
		<th>Titre</th>
		<th>Quantité</th>
		<th>Prix unitaire</th>
		<th>Total</th>
		<th>Supprimer</th>
	</tr>
	<?php if(empty($_SESSION['panier']['id_produit'])) : ?>
	<tr>
		<td colspan="6">Votre panier est vide</td>
	</tr>
	<?php else : ?>
	<?php for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) : ?>
		<tr>
			<td><img src="<?= RACINE_SITE ?>photo/<?= $_SESSION['panier']['photo'][$i] ?>" height="30" /></td>
			<td><?= $_SESSION['panier']['titre'][$i] ?></td>
			
			
			
			<td>
			<a href="?action=decrementation&id_produit=<?= $_SESSION['panier']['id_produit'][$i] ?>"><img src="img/moins.png" width="15" /></a>
			
			<span style="padding: 3px; border: solid 1px black; text-align;: center; width: 20px; display: inline-block"><?= $_SESSION['panier']['quantite'][$i] ?></span>
			
			<a href="?action=incrementation&id_produit=<?= $_SESSION['panier']['id_produit'][$i] ?>"><img src="img/plus.png" width="15" /></a>
			
			</td>
			
			
			
			
			
			<td><?= $_SESSION['panier']['prix'][$i] ?>€</td>
			<td><?= $_SESSION['panier']['prix'][$i] *  $_SESSION['panier']['quantite'][$i] ?>€</td>
			<td>
				<a href="?action=supprimer&id_produit=<?= $_SESSION['panier']['id_produit'][$i] ?>"><img src="img/delete.png" height="22"/></a>
			</td>
		</tr>
	<?php endfor; ?>
	<tr>
		<td colspan="4">Montant Total</td>
		<td colspan="2"><?= montantTotal() ?>€</td>
	</tr>
	<tr>
		<!-- si user est connecté btn paiement-->
		<?php if(userConnecte()) : ?>
			<td colspan="6">
				<form method="post" action="">	
					<input type="hidden" name="montant" value="<?= montantTotal() ?>" />
					<input type="submit" value="payer" name="paiement" />
				</form>	
			</td>
		<!--  sinon btn connexion -->
		<?php else : ?>
			<tr>
				<td colspan="6">Veuillez vous <a href="connexion.php">Connecter</a> ou  vous <a href="inscription.php">inscrire</a> pour payer votre panier</td>
			</tr>
		<?php endif; ?>
	</tr>
	<tr>
		<td colspan="6"><a href="?action=vider">Vider votre panier</a></td>
	</tr>
	<?php endif; ?>
</table>





<?php 
require_once('inc/footer.inc.php');
?>