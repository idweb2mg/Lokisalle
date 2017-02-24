<?php
require_once('inc/init.inc.php');

require_once('inc/header.inc.php');
$req= "
	SELECT s.url_photo, s.titre, s.description, p.prix, p.date_arrivee, p.date_depart, s.id_salle
 	FROM salle s, produit p
	WHERE s.id_salle = p.id_salle
 	";

$resultat = $pdo -> query($req); 


?>


<!-- Mon contenu HTML -->
<h1>Accueil</h1>

<?php

while($salles = $resultat -> fetch(PDO::FETCH_ASSOC)){

	echo '<div class="col-sm-6 col-lg-3 col-md-4">';
	echo '	<div class="thumbnail">';
	echo '		<img src=" ' . RACINE_SITE . 'photo/'. $salles['url_photo'] .' " alt="">';
	echo '		<div class="caption">';
	echo '			<h4 class="pull-right">'. $salles['prix'] .' €</h4>';
	echo '			<h4><a href="fiche_produit.php?id_salle='. $salles['id_salle'] .'">'. $salles['titre'] .'</a>';
	echo '			</h4>';
	echo '			<ul>';
	echo '				<li>Date d\'arrivée : '. $salles['date_arrivee'] .'</li>';
	echo '				<li>Date de départ : '. $salles['date_depart'] .'</li>';
	echo '			</ul>';
	echo '		</div>';
	echo '		<div class="ratings">';
	echo '			<p class="pull-right">15 reviews</p>';
	echo '			<p>';
	echo '				<span class="glyphicon glyphicon-star"></span>';
	echo '				<span class="glyphicon glyphicon-star"></span>';
	echo '				<span class="glyphicon glyphicon-star"></span>';
	echo '				<span class="glyphicon glyphicon-star"></span>';
	echo '				<span class="glyphicon glyphicon-star"></span>';
	echo '			</p>';
	echo '		</div>';
	echo '	</div>';
	echo '</div>';

}

 ?>


<?php
require_once('inc/footer.inc.php');
?>