﻿<?php
/* === profil.php ===*/
require_once('inc/init.inc.php') ;

//SUPPRIMER
if(isset($_GET['action']) && $_GET['action'] == 'supprimer'){
	$id_membre = $_SESSION['membre']['id_membre'] ;
	$resultat = $pdo -> exec("DELETE FROM membre WHERE id_membre = $id_membre") ; 
	unset($_SESSION['membre']) ;
}

//Redirection si pas connecté 
if(!userConnecte()){ // Si la fonction me retourne FALSE
	header('location:connexion.php') ;
}

//Pour afficher les infos
extract($_SESSION['membre']) ;

debug($_SESSION) ;




// VOIR
// AFFICHER LES DETAILS D'UNE COMMANDE
/*
if(isset($_GET['action']) && $_GET['action'] == 'afficher'){

	

	$id_membre = $_SESSION['membre']['id_membre'] ;
	$req = "
	SELECT 
	FROM commande c,produit p, salle s
	WHERE c.id_membre = $id_membre " ;

	$resultat = $pdo -> query($req) ; 

	if($resultat -> rowCount() > 0){

	$contenu .= '<table border="1">' ;
	$contenu .= '<tr>' ;
	for($i = 0 ; $i < $resultat -> columnCount() ; $i++){
		$meta = $resultat -> getColumnMeta($i) ;
		$contenu .= '<th>' . $meta['name'] . '</th>' ;
	}
	$contenu .= '<th>Actions</th>' ;
	$contenu .= '</tr>' ;
	while($commandes = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
		$contenu .= '<tr>' ; 
		foreach($commandes as $indice => $valeur){
			if($indice == 'photo'){
				$contenu .= '<td><img src="' . RACINE_SITE . 'photo/' . $valeur . '" height="100"/></td>' ;
			}
			else{
				$contenu .= ' <td>' . $valeur . '</td>' ;
			}
		}
		$contenu .= '<td><a href="?action=voir&id_commande='. $commandes['id_commande'] .'"><img src="' . RACINE_SITE . 'img/eye.png" width="25"/></a></td>' ;
		$contenu .= '</tr>' ;
	}
	$contenu .= '</table>' ;
	}
	else{
		$contenu .= '<p>Vous n\'avez jamais commandé sur le site</p>' ;
	}


} // FIN if(isset($_GET['action']) && $_GET['action'] == 'afficher')


*/





//Traitement pour afficher les détails des commandes de l'utilisateur:
if(isset($_GET['action']) && $_GET['action'] == 'afficher'){
	if(isset($_GET['id_commande']) && !empty($_GET['id_commande']) && is_numeric($_GET['id_commande'])){
		
		$req = "
		SELECT 
		  p.id_produit
		, s.titre
		, p.date_arrivee
		, p.date_depart
		, p.prix
		, p.etat
		FROM produit p, salle s
		WHERE s.id_salle = p.id_salle
		AND c.id_commande = '$_GET[id_commande]'
		AND c.id_produit = p.id_produit
		";
echo "FIN J1 EVAL"		 ;


		$resultat = $pdo -> prepare("
		SELECT p.photo, p.titre, dc.* 
		FROM details_commande dc
		LEFT JOIN produit p ON p.id_produit = dc.id_produit
		WHERE dc.id_commande = :id") ;	

		$resultat = $pdo -> prepare($req) ;		
		$resultat -> bindParam(':id', $_GET['id_commande'], PDO::PARAM_INT) ;
		$resultat -> execute() ;
		
		if($resultat -> rowCount() > 0){
			$contenu .= '<hr/><h2>Détails de la commande N°' . $_GET['id_commande'] . '</h2>' ;
			$contenu .= '<table border="1">' ;
			$contenu .= '<tr>' ;
			for($i = 0 ; $i < $resultat -> columnCount() ; $i++){
				$meta = $resultat -> getColumnMeta($i) ;
				$contenu .= '<th>' . $meta['name'] . '</th>' ;
			}
			$contenu .= '</tr>' ;
			while($commandes = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
				$contenu .= '<tr>' ; 
				foreach($commandes as $indice => $valeur){
					if($indice == 'photo'){
						$contenu .= '<td><img src="' . RACINE_SITE . 'photo/' . $valeur . '" height="100"/></td>' ;
					}
					else{
						$contenu .= ' <td>' . $valeur . '</td>' ;
					}
				}
				$contenu .= '</tr>' ;
			}
			$contenu .= '</table>' ;
		}	
	}	
}

// Récupération de l'historique descommandes du pseudo donné
	$pseudo = $_SESSION['membre']['pseudo'] ;
$req2 = "
		SELECT c.id_commande, c.date_enregistrement 
		FROM commande c, membre m 
		WHERE c.id_membre = m.id_membre 
		AND m.pseudo = '$pseudo'
		" ;
//echo $pseudo ;
$resultat = $pdo -> query($req2); 

$contenu .= '<table border="1">';
$contenu .= '<tr>';

for($i = 0; $i < $resultat -> columnCount(); $i++){

	$meta = $resultat -> getColumnMeta($i);
	$contenu .= '<th>' . $meta['name'] . '</th>';
}
$contenu .= '<th>Action</th>';
$contenu .= '</tr>';
while($commandes = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
	$contenu .= '<tr>'; 
	foreach($commandes as $indice => $valeur){
		$contenu .= ' <td>' . $valeur . '</td>';
	}
	$contenu .= '<td><a href="?action=afficher&id_commande='. $commandes['id_commande'] .'" /><img src="' . RACINE_SITE . 'img/eye.png" width="25"/></a></td>';
	
	$contenu .= '</tr>';
}
$contenu .= '</table>';


$page = 'Profil' ;
require_once('inc/header.inc.php') ; 
?>

<!-- Contenu de la page -->
<h1>Profil de <?= $pseudo ?></h1>

<div class="profil">
	<p>Bonjour <?= $pseudo?> !</p><br/>
	
	<div class="profil_img">
		<img src="img/default.png"/>
	</div>
	<div class="profil_infos">
		<ul>
			<li>Pseudo : <b><?= $pseudo ?></b></li>
			<li>Prénom : <b><?= $prenom ?></b></li>
			<li>Nom: <b><?= $nom ?></b></li>
			<li>email : <b><?= $email ?></b></li>
			<li>Civilité : <b><?php ($civilite == 'm') ? $var = 'Homme' :  $var = 'Femme'; echo $var ;  ?></b></li> 
			<li>Statut : <b><?php ($statut == 1) ? $var = 'admin' :  $var = 'membre'; echo $var ;  ?></b></li>
			<li>Date enregistrement : <b><?= $date_enregistrement ?></b></li>			
		</ul>
	</div>
	<div class="liste_commande">
		<?= $contenu ?>
	</div>
<!--
	<a href="membre.php">Modifier mon profil</a><br/>
	<a href="?action=supprimer">Supprimer mon compte</a><br/>
-->	
</div>

<?php
require_once('inc/footer.inc.php') ;
?>