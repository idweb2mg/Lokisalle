<?php
require_once('../inc/init.inc.php');

// redirection si pas admin
if(!userAdmin()){
	header('location:../connexion.php');
}

//Traitement pour modifier l'etat d'une commande
if(isset($_POST['modifier'])){
	
	$resultat = $pdo -> prepare("UPDATE commande set etat = :etat WHERE id_commande = :id");
	$resultat -> bindParam(':etat', $_POST['etat'], PDO::PARAM_STR);
	$resultat -> bindParam(':id', $_POST['id_commande'], PDO::PARAM_INT);
	
	if($resultat -> execute()){
		$msg .= '<div class="validation">La commande N°'. $_POST['id_commande'] .' a bien été modifiée !</div>';
		
		// Pour un envoyer un mail à l'acheteur il nous faut plus d'infos sur la commande et sur l'utilisateuro= : 
		$resultat = $pdo -> prepare("
		SELECT c.*, m.*
		FROM commande c, membre m
		WHERE c.id_membre = m.id_membre
		AND c.id_commande = :id
		");
		
		$resultat -> bindParam(':id', $_GET['id_commande'], PDO::PARAM_INT);
		if($resultat -> execute())
		{
			$infos_commande = $resultat -> fetch(PDO::FETCH_ASSOC);
			mail();
		}	
	}
}



//traitement pour afficher toutes les infos de toutes les commandes : 

if(isset($_POST['tri'])){
	switch($_POST['choix']){
		case 'date' : 
			$req = "
			SELECT c.*, m.pseudo, m.ville, m.code_postal, m.adresse
			FROM commande c 
			LEFT JOIN membre m ON m.id_membre = c.id_membre ORDER BY c.date_enregistrement ASC";
		break;
		
		case 'montant' : 
			$req = "
			SELECT c.*, m.pseudo, m.ville, m.code_postal, m.adresse
			FROM commande c 
			LEFT JOIN membre m ON m.id_membre = c.id_membre ORDER BY c.montant";
		break;
		
		case 'etat' : 
			$req = "
			SELECT c.*, m.pseudo, m.ville, m.code_postal, m.adresse
			FROM commande c 
			LEFT JOIN membre m ON m.id_membre = c.id_membre ORDER BY c.etat";
		break;
		
		default : 
			$req = "
			SELECT c.*, m.pseudo, m.ville, m.code_postal, m.adresse
			FROM commande c 
			LEFT JOIN membre m ON m.id_membre = c.id_membre";
		break;	
	}
}
else{
	$req = "
	SELECT c.*, m.pseudo, m.ville, m.code_postal, m.adresse
	FROM commande c 
	LEFT JOIN membre m ON m.id_membre = c.id_membre";
}
$resultat = $pdo -> query($req); 

$contenu .= '<table border="1">';
$contenu .= '<tr>';
for($i = 0; $i < $resultat -> columnCount(); $i++){
	$meta = $resultat -> getColumnMeta($i);
	$contenu .= '<th>' . $meta['name'] . '</th>';
}
$contenu .= '<th colspan="2">Actions</th>';
$contenu .= '</tr>';
while($commandes = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
	$contenu .= '<tr>'; 
	foreach($commandes as $indice => $valeur){
		if($indice == 'photo'){
			$contenu .= '<td><img src="' . RACINE_SITE . 'photo/' . $valeur . '" height="100"/></td>';
		}
		else{
			$contenu .= ' <td>' . $valeur . '</td>';
		}
	}
	$contenu .= '<td><a href="?action=modifier&id_commande='. $commandes['id_commande'] .'"><img src="' . RACINE_SITE . 'img/edit.png"/></a></td>';
	$contenu .= '<td><a href="?action=voir&id_commande='. $commandes['id_commande'] .'"><img src="' . RACINE_SITE . 'img/eye.png" width="25"/></a></td>';
	$contenu .= '</tr>';
}
$contenu .= '</table>';


//Afficher les détails d'une commande : 
if(isset($_GET['action']) && $_GET['action'] == 'voir'){
	if(isset($_GET['id_commande']) && !empty($_GET['id_commande']) && is_numeric($_GET['id_commande'])){
		
		
		$resultat = $pdo -> prepare("
		SELECT p.photo, p.titre, dc.* 
		FROM details_commande dc
		LEFT JOIN produit p ON p.id_produit = dc.id_produit
		WHERE dc.id_commande = :id");	
		
		$resultat -> bindParam(':id', $_GET['id_commande'], PDO::PARAM_INT);
		$resultat -> execute();
		
		if($resultat -> rowCount() > 0){
			$contenu .= '<hr/><h2>Détails de la commande N°' . $_GET['id_commande'] . '</h2>';
			$contenu .= '<table border="1">';
			$contenu .= '<tr>';
			for($i = 0; $i < $resultat -> columnCount(); $i++){
				$meta = $resultat -> getColumnMeta($i);
				$contenu .= '<th>' . $meta['name'] . '</th>';
			}
			$contenu .= '</tr>';
			while($commandes = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
				$contenu .= '<tr>'; 
				foreach($commandes as $indice => $valeur){
					if($indice == 'photo'){
						$contenu .= '<td><img src="' . RACINE_SITE . 'photo/' . $valeur . '" height="100"/></td>';
					}
					else{
						$contenu .= ' <td>' . $valeur . '</td>';
					}
				}
				$contenu .= '</tr>';
			}
			$contenu .= '</table>';
		}	
	}	
}



// traitement pour modifier le statut d'une commande : 
if(isset($_GET['action']) && $_GET['action'] == 'modifier'){
	if(isset($_GET['id_commande']) && !empty($_GET['id_commande']) && is_numeric($_GET['id_commande'])){

	$resultat = $pdo -> prepare("SELECT * FROM commande WHERE id_commande = :id");	
	$resultat -> bindParam(':id', $_GET['id_commande'], PDO::PARAM_INT);
	$resultat -> execute();
		
	if($resultat -> rowCount() > 0){
		$commande = $resultat -> fetch(PDO::FETCH_ASSOC);
		
		$contenu .= '<hr/><h2>Modifier la commande N°'. $_GET['id_commande'] .'</h2>';
		$contenu .= '<form action="" method="post">';
		$contenu .= '<input type="hidden" name="id_commande" value="'. $commande['id_commande'] .'"/>';
		$contenu .= '<select name="etat">';
		
		$contenu .= ' <option ';
		if($commande['etat'] == 'en cours de traitement'){$contenu .= ' selected ';}
		$contenu .= ' value="en cours de traitement">En cours de traitement</option>';
		
		$contenu .= ' <option ';
		if($commande['etat'] == 'envoyé'){$contenu .= ' selected ';}
		$contenu .= ' value="envoyé">Envoyé</option>';
		
		
		$contenu .= '	<option ';
		if($commande['etat'] == 'livré'){$contenu .= ' selected ';}
		$contenu .= ' value="livré">Livré</option>';
		$contenu .= '</select>';
		
		$contenu .= '<input type="submit" value="Modifier" name="modifier"/>';
		
		$contenu .= '</form>';
	}
	}
}



$page = 'Gestion Commandes';
require_once('../inc/header.inc.php');
?>
<h1>Gestion des commandes</h1>
<form action="" method="post">
	<select name="choix">
		<option value="date">Par date</option>
		<option value="montant">Par Montant</option>
		<option value="etat">Par Etat</option>
	</select>
	<input type="submit" name="tri" value="Trier"/>
</form>
<?= $msg ?>
<?= $contenu ?>













<?php
require_once('../inc/footer.inc.php');
?>