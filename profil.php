<?php
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

//debug($_SESSION) ;


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
		FROM produit p, salle s, commande c
		WHERE s.id_salle = p.id_salle
		AND c.id_commande = '$_GET[id_commande]'
		AND c.id_produit = p.id_produit
		";

		$resultat = $pdo -> prepare($req) ;		
		$resultat -> bindParam(':id', $_GET['id_commande'], PDO::PARAM_INT) ;
		$resultat -> execute() ;
	
		if($resultat -> rowCount() > 0){
			$contenu2 .= '<hr/><h2>Détails de la commande N°' . $_GET['id_commande'] . '</h2>' ;
			$contenu2 .= '<table border="1">' ;
			$contenu2 .= '<tr>' ;
			for($i = 0 ; $i < $resultat -> columnCount() ; $i++){
				$meta = $resultat -> getColumnMeta($i) ;
				$contenu2 .= '<th>' . $meta['name'] . '</th>' ;
			}
			$contenu2 .= '</tr>' ;
			while($commandes = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
				$contenu2 .= '<tr>' ; 
				foreach($commandes as $indice => $valeur){
					if($indice == 'photo'){
						$contenu2 .= '<td><img src="' . RACINE_SITE . 'photo/' . $valeur . '" height="100"/></td>' ;
					}
					else{
						$contenu2 .= ' <td>' . $valeur . '</td>' ;
					}
				}
				$contenu2 .= '</tr>' ;
			}
			$contenu2 .= '</table>' ;
		}	
	}	
}

// Récupération de l'historique descommandes du pseudo donné

$req2 = "
		SELECT c.id_commande, c.date_enregistrement 
		FROM commande c, membre m 
		WHERE c.id_membre = m.id_membre 
		AND m.pseudo = '$pseudo'
		" ;
//echo $pseudo ;
$resultat = $pdo -> query($req2); 


$contenu1 .= '<h2>Liste des commandes de ' . $pseudo . ' </h2>' ;
$contenu1 .= '<table border="1">';
$contenu1 .= '<tr>';

for($i = 0; $i < $resultat -> columnCount(); $i++){

	$meta = $resultat -> getColumnMeta($i);
	$contenu1 .= '<th>' . $meta['name'] . '</th>';
}
$contenu1 .= '<th>Action</th>';
$contenu1 .= '</tr>';
while($commandes = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
	$contenu1 .= '<tr>'; 
	foreach($commandes as $indice => $valeur){
		$contenu1 .= ' <td>' . $valeur . '</td>';
	}
	$contenu1 .= '<td><a href="?action=afficher&id_commande='. $commandes['id_commande'] .'" /><img src="' . RACINE_SITE . 'img/eye.png" width="25"/></a></td>';
	
	$contenu1 .= '</tr>';
}
$contenu1 .= '</table>';


$page = 'Profil' ;
require_once('inc/header.inc.php') ; 
?>

<!-- contenu1 de la page -->
<h1>Profil de <?= $pseudo ?></h1>

<div class="profil">

	<p>Bonjour <?= $pseudo?> !</p><br/>
	<h1> Profil de <?= $pseudo?>	</h1>
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
		</ul><br>
		<a href="membre.php"><b>=> Modifier votre profil</b></a>
	</div>
	<div class="liste_commande">
		<?= $contenu1 ?>
		<?= $contenu2 ?>
	</div>
<!--
	<a href="membre.php">Modifier mon profil</a><br/>
	<a href="?action=supprimer">Supprimer mon compte</a><br/>
-->	
</div>

<?php
require_once('inc/footer.inc.php') ;
?>