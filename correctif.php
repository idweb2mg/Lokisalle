<?php

require('inc/init.inc.php');

$resultat = $pdo -> query("SELECT * FROM membre");

while($membres = $resultat -> fetch(PDO::FETCH_ASSOC)){
	$result = $pdo->exec("UPDATE membre set photo = 'default.jpg' WHERE id_membre = $membres[id_membre]");
}