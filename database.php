<?php
	
	/*
	*	Configuration BDD et PDO
	*/
	// Type
	define( 'DBTYPE' , 'mysql' );
	// Utilisateur
	define( 'DBUSER' , 'root' );
	// Mot de passe
	define( 'DBPASS' , '' );
	// Hôte
	define( 'DBHOST' , 'localhost' );
	// Nom de la base
	define( 'DBNAME' , 'framework' );
	// Préfixe des tables
	define( 'DBPREF' , 'prefix_' );
	// Encodage PHP/BDD
	define( 'DBCHAR' , 'UTF8' );
	// Gestion des erreurs
	define( 'ERRMOD' , PDO::ERRMODE_EXCEPTION );
	// Fetch mode
	define( 'FETMOD' , PDO::FETCH_OBJ );
	// Time locale
	define( 'DBLOCALE' , 'fr_FR' );
	
	/*
	*	Connexion BDD
	*/
	try {
		$pdoOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET lc_time_names = "'.DBLOCALE.'"';
		$pdoOptions[PDO::ATTR_ERRMODE] = ERRMOD;
		$pdoOptions[PDO::ATTR_DEFAULT_FETCH_MODE] = FETMOD;
		$bdd = new PDO(DBTYPE.':host='.DBHOST.';dbname='.DBNAME.';charset='.DBCHAR, DBUSER, DBPASS, $pdoOptions);
	}
	catch(Exception $e) {
		echo M_DBCON;
		exit();
	}
  
  ?>
  
