<?php
/* === init.inc.php ===*/

// CONNEXION BDD
$pdo = new PDO('mysql:host=localhost;dbname=lokisalle', 'root', '', array(
	PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
));

// SESSION
session_start();

// CHEMIN
define('RACINE_SITE', '/lokisalle/lokisalle/');

// VARIABLES
$msg = ''; 
$page = '';
$contenu1 = '';
$contenu2 = '';

// AUTRES INCLUSIONS
require_once('fonctions.inc.php');

