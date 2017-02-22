<?php

// Fonction Debug (amélioration du print_r())
function debug($arg){
	echo '<div style="color: white; font-weight: bold; padding: 10px; background:#' . rand(111111, 999999) . '">';
	$trace = debug_backtrace(); // debug_backtrace me retourne des infos sur l'emplacement où est EXECUTER cette fonction. Nous retourne un array multidimentionnel. 
	echo 'Le debug a été demandé dans le fichier : ' . $trace[0]['file'] . ' à la ligne : ' . $trace[0]['line'] . '<hr/>';

	echo '<pre>';
	print_r($arg);
	echo '</pre>';
	
	echo '</div>';
}

// Fonction pour voir si l'utilisateur est connecté
function userConnecte(){
	if(isset($_SESSION['membre'])){
		return TRUE;
	}
	else{
		return FALSE;
	}
	// S'il existe une session/membre, c'est que l'utilisateur est connecté. Je retourne TRUE, sinon, je retourne ELSE. 
}

// Fonction pour voir si l'utilisteur est admin
function userAdmin(){
	if(userConnecte() && $_SESSION['membre']['statut'] == 1){
		return TRUE;
	}
	else{
		return FALSE;
	}
	// Si l'utilisateur est connecté et que son statut c'est "1" alors je retourne TRUE. Sinon je retourne FALSE. 
}

// Fonction pour créer un panier

function creationPanier(){
	if(!isset($_SESSION['panier'])){
		$_SESSION['panier'] = array();
		$_SESSION['panier']['id_produit'] = array(); 
		$_SESSION['panier']['quantite'] = array(); 
		$_SESSION['panier']['photo'] = array(); 
		$_SESSION['panier']['titre'] = array(); 
		$_SESSION['panier']['prix'] = array(); 
	}
	return true;
}

// Fonction pour ajouter un produit au panier
function ajouterProduit($id_produit, $quantite, $titre, $photo, $prix){
	creationPanier();
	
	// Nous devons vérifier que le produit en cours d'ajout n'éxiste pas déjà dans notre panier : 
	$positionPdt = array_search($id_produit, $_SESSION['panier']['id_produit']);
	//array_seach est une fonction qui me permet de chercher une info dans un array. Si elle trouve, elle me retourne son emplacement sinon, elle me retourne FALSE. 
	
	if($positionPdt !== FALSE){
		$_SESSION['panier']['quantite'][$positionPdt] += $quantite;
	}
	else {
		$_SESSION['panier']['quantite'][] = $quantite;
		$_SESSION['panier']['id_produit'][] = $id_produit;
		$_SESSION['panier']['photo'][] = $photo;
		$_SESSION['panier']['titre'][] = $titre;
		$_SESSION['panier']['prix'][] = $prix;
	}
}

// Fonction pour calculer le nombre de produit dans le panier

function quantitePanier(){
	$quantite = 0; 
	if(isset($_SESSION['panier']) && !empty($_SESSION['panier']['quantite'])){
		for($i = 0; $i < count($_SESSION['panier']['quantite']); $i++){
			$quantite += $_SESSION['panier']['quantite'][$i];
		}
	}
	if($quantite != 0){
		return $quantite;
	}
}

// Fonction pour calculer le montant total d'un panier 
function montantTotal(){
	$total = 0; 
	
	if(isset($_SESSION['panier']) && !empty($_SESSION['panier']['prix'])){
		for($i=0; $i < count($_SESSION['panier']['prix']); $i++){
			$total += $_SESSION['panier']['prix'][$i] * $_SESSION['panier']['quantite'][$i];	
		}
	}
	
	if($total != 0){
		return $total;
	}
}

// Fonction pour retirer un produit du tableau : 
function retirerProduit($id_produit){
	
	$position_pdt_a_supprimer = array_search($id_produit, $_SESSION['panier']['id_produit']);
	// Je cherche la position du produit à supprimer grâce à son id dans la liste de tous les id des produit du panier. 
	
	if($position_pdt_a_supprimer !== FALSE){
		array_splice($_SESSION['panier']['id_produit'], $position_pdt_a_supprimer, 1);
		array_splice($_SESSION['panier']['prix'], $position_pdt_a_supprimer, 1);
		array_splice($_SESSION['panier']['quantite'], $position_pdt_a_supprimer, 1);
		array_splice($_SESSION['panier']['titre'], $position_pdt_a_supprimer, 1);
		array_splice($_SESSION['panier']['photo'], $position_pdt_a_supprimer, 1);
	}
}








