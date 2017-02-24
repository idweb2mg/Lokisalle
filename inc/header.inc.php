<!Doctype html>
<html>
    <head>
        <title>LOKISALLE - <?= $page ?></title>
        <link type="text/css" rel="stylesheet" href="<?= RACINE_SITE ?>css/bootstrap.css" />
        <link rel="stylesheet" href="<?= RACINE_SITE ?>css/style.css"/>
    </head>
    <body>    
        <header>
			<div class="conteneur">                      
				<span>
					<a href="" title="Lokisalle">LOKISALLE</a>
                </span>
				<nav>
				<?php if(userConnecte()):?>
					<a <?= ($page == 'Profil') ? 'class="active"' : '' ?> href="<?= RACINE_SITE ?>profil.php">Profil</a>
					<a <?= ($page == 'Produit') ? 'class="active"' : '' ?> href="<?= RACINE_SITE ?>produit.php">Produit</a>
					<a <?= ($page == 'Panier') ? 'class="active"' : '' ?> href="<?= RACINE_SITE ?>panier.php">Panier<?php if(quantitePanier()) : ?><span class="bulle"><?= quantitePanier()?></span><?php endif ?></a>
					<a href="<?= RACINE_SITE ?>connexion.php?action=deconnexion">Deconnexion</a>
				<?php else : ?>
					<a <?= ($page == 'Inscription') ? 'class="active"' : '' ?> href="<?= RACINE_SITE ?>inscription.php">Inscription</a>
					<a <?= ($page == 'Boutique') ? 'class="active"' : '' ?> href="<?= RACINE_SITE ?>produit.php">Produit</a>
					<a <?= ($page == 'Panier') ? 'class="active"' : '' ?> href="<?= RACINE_SITE ?>panier.php">Panier<?php if(quantitePanier()) : ?><span class="bulle"><?= quantitePanier()?></span><?php endif ?></a>
					
					
					<a <?= ($page == 'Connexion') ? 'class="active"' : '' ?> href="<?= RACINE_SITE ?>connexion.php">Connexion</a>
				<?php endif; ?>
				<?php if(userAdmin()) : ?>
					<a <?= ($page == 'Gestion Salle') ? 'class="active"' : '' ?> href="<?= RACINE_SITE ?>admin/gestion_salle.php">Gestion Salle</a>
					<a <?= ($page == 'Gestion Boutique') ? 'class="active"' : '' ?> href="<?= RACINE_SITE ?>admin/gestion_produit.php">Gestion Produit</a>
					<a <?= ($page == 'Gestion Membres') ? 'class="active"' : '' ?> href="<?= RACINE_SITE ?>admin/gestion_membres.php">Gestion Membres</a>
					<a <?= ($page == 'Gestion Commandes') ? 'class="active"' : '' ?> href="<?= RACINE_SITE ?>admin/gestion_commandes.php">Gestion Commandes</a>
				<?php endif; ?>
				</nav>
			</div>
        </header>
        <section>
			<div class="conteneur">