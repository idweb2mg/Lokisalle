CREATE TABLE salle (
   id_salle    int(3)             NOT NULL auto_increment
  ,titre       varchar(200)       NOT NULL
  ,description text               NOT NULL
  ,photo       varchar(200)       NOT NULL
  ,pays        varchar(20)        NOT NULL
  ,ville       varchar(20)        NOT NULL
  ,code_postal int(5)             NOT NULL
  ,adresse     varchar(50)        NOT NULL
  ,capacite    int(3)             NOT NULL
  ,categorie   enum('r','b','f')  NOT NULL  
-- catégorie : r=réception, b=bureau, f=formation  
  ,PRIMARY KEY  (id_salle)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1
;


CREATE TABLE membre (
   id_membre            int(3)        NOT NULL auto_increment
  ,pseudo               varchar(20)   NOT NULL
  ,mdp                  varchar(128)  NOT NULL
  ,nom                  varchar(20)   NOT NULL
  ,prenom               varchar(20)   NOT NULL
  ,email                varchar(50)   NOT NULL
  ,civilite             enum('m','f') NOT NULL
-- civilite :m=homme, f=femme
  ,statut               int(1)        NOT NULL 
  ,date_enregistrement  datetime      NOT NULL 
  ,PRIMARY KEY  (id_membre)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1
;

CREATE TABLE produit (
   id_produit     int(3)              NOT NULL auto_increment
  ,id_salle       int(3)              NOT NULL
  ,date_arrivee   datetime            NOT NULL
  ,date_depart    datetime            NOT NULL 
  ,prix           int(3)              NOT NULL 
  ,etat           enum('l','r')       NOT NULL 
-- etat du produit (salle/disponibilité) : l=libre, r=réservé
  ,PRIMARY KEY  (id_produit)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1
;

CREATE TABLE commande (
   id_commande          int(3)        NOT NULL auto_increment
  ,id_membre            int(3)        NOT NULL
  ,id_produit           int(3)        NOT NULL
  ,date_enregistrement datetime       NOT NULL
  ,PRIMARY KEY  (id_commande)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1
;

CREATE TABLE avis (
   id_avis             int(3)        NOT NULL auto_increment
  ,id_membre           int(3)        NOT NULL
  ,id_salle            int(3)        NOT NULL
  ,commentaire         text          NOT NULL
  ,note                int(2)        NOT NULL
  ,date_enregistrement datetime      NOT NULL  
  ,PRIMARY KEY  (id_avis)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1
;
