<?php
	/*
	*	Débogage PHP
	*/
	$debug = true;
	if( $debug ) {
		error_reporting(E_ALL);
	} else {
		error_reporting(0);
	}
	
	/*
	*	Initialisation timezone
	*/
	date_default_timezone_set( 'Europe/Paris' );
	
	/*
	*	Messages par défaut
	*/
	define( 'M_DBCON', '<h1>Oops</h1><p>La connexion à la base de données n\'a pas pu être établie.</p><p>Impossible de travailler dans ces conditions ...</p><p>Contactez l\'administrateur pour faire corriger ce problème.</p>' );
	define( 'M_LOGOUT', 'Vous êtes à présent déconnecté.' );
	define( 'M_LOGIN', 'Vous êtes à présent connecté.' );
	define( 'M_ERRLOGIN', 'Vous êtes déjà connecté.' );
	define( 'M_SESSERR', 'Une erreur de session est apparue, veuillez vous reconnecter.' );
	define( 'M_IDENTERR', 'Identifant inexistant, veuillez réessayer.' );
	define( 'M_VALIDERR', 'Votre compte n\'est pas encore validé, un email de validation vous a déjà été envoyé lors de votre inscription. Vérifiez votre boîte mail et cliquez sur le lien proposé.' );
	define( 'M_TOKENERR', 'Le lien d\'activation utilisé n\'est pas valide ou a déjà été utilisé, essayez de vous connecter directement sur la page d\'accueil.' );
	define( 'M_PASSERR', 'Mot de passe incorrect, veuillez réessayer.' );
	define( 'M_DBITEMERR', 'L\'élément demandé n\'existe pas.' );
	define( 'M_TMPLERR', 'Le fichier template n\'existe pas pour la page demandée : <em>%s</em>.' );
	define( 'M_VIEWERR', 'Le fichier template de vue n\'existe pas pour la page demandée : <em>%s</em>.' );
	define( 'M_OPTSERR', 'Erreur lors de la récupération des options.' );
	define( 'M_OPTERR', 'L\'option <em>%s</em> n\'existe pas.' );
	define( 'M_USERSERR', 'Erreur lors de la récupération des utilisateurs.' );
	define( 'M_ITEMSERR', 'Erreur lors de la récupération des éléments <em>%s</em>.' );
	define( 'M_COUNTERR', 'Erreur lors du décompte des éléments <em>%s</em>.' );
	define( 'M_ITEMERR', 'Erreur lors de la récupération de l\'élément <em>%s</em>.' );
	define( 'M_MENUSERR', 'Erreur lors de la récupération des identifiants de menu.' );
	define( 'M_MENUERR', 'Erreur lors de la récupération du menu <em>%d</em>.' );
	define( 'M_ITEMNEW', 'Le nouvel élément <em>%s</em> a bien été créé.' );
	define( 'M_ITEMNEWERR', 'Erreur lors de la création de l\'élément <em>%s</em>.' );
	define( 'M_ITEMSET', 'L\'élément <em>%s</em> a bien été mis à jour.' );
	define( 'M_ITEMSETERR', 'Erreur lors de la mise à jour de l\'élément <em>%s</em>.' );
	define( 'M_ITEMSETKEYERR', 'L\'élément <em>%s</em> ne peut être créé car il existe déjà un enregistrement pour l\'identifiant <em>%s</em>.' );
	define( 'M_ITEMDEL', 'L\'élément <em>%s</em> a bien été supprimé.' );
	define( 'M_ITEMDELKEYERR', 'L\'élément <em>%s</em> ID = %d ne peut être supprimé car il est lié à d\'autres éléments.' );
	define( 'M_ITEMDELERR', 'Erreur lors de la suppression de l\'élément <em>%s</em> ID = %d.' );
	define( 'M_RELERR', 'Le fichier template de relation vers <em>%s</em> n\'existe pas.' );
	define( 'M_RELNEWERR', 'Erreur lors de la création de la relation entre <em>%s</em> et <em>%s</em>.' );
	define( 'M_IDERR', 'Erreur lors de la récupération de l\'identifiant de l\'élément <em>%s</em>.' );
	define( 'M_UPIDERR', 'Erreur lors de l\'incrémentation de l\'identifiant de l\'élément <em>%s</em>.' );
	define( 'M_CLASSERR', 'Erreur lors de la récupération des relations <em>%s</em>. La classe ne prévoit pas cette relation.' );
	define( 'M_RELSETERR', 'Erreur lors de l\'enregistrement des relations <em>%s</em> / <em>%s</em>.' );
	define( 'M_RELDELERR', 'Erreur lors de la suppression de la relation <em>%s</em> / <em>%s</em>.' );
	define( 'M_ACCESSERR', 'Vous n\'avez pas les droits suffisants pour effectuer cette action.' );
	define( 'M_EMAILOK', 'La création de votre compte a bien été prise en compte, un email de validation vous a été envoyé à l\'adresse suivante : <em>%s</em>. Cliquez sur le lien présent dans cet email pour finaliser votre inscription sur le site. ' );
	define( 'M_EMAILERR', 'La création de votre compte a bien été prise en compte, mais nous n\'avons pas réussi à vous envoyer l\'email de validation à l\adresse suivante : <em>%s</em>. Merci de nous contacter directement pour faire valider votre compte : <a href="mailto:%s?subject=Validation compte email">%s</a>.' );
	define( 'M_VALEMAILOK', 'Votre compte a bien été activé, vous pouvez désormais vous connecter.' );
	define( 'M_VALEMAILERR', 'Une erreur est survenue lors de la validation de votre compte. Merci de nous contacter directement pour faire valider votre compte : <a href="mailto:%s?subject=Validation compte email">%s</a>.' );
	define( 'M_VALOK', 'Votre compte est déjà validé.' );
	define( 'M_IMPERR', 'Erreur lors de la génération du document PDF.' );
	
	/*
	*	Configuration répertoires
	*/
	// URL du site
	define( 'SITEURL'	, 'http://localhost/projets/framework/' );
	// Chemin depuis la racine du serveur web
	define( 'SITEDIR'	, 'projets/framework/' );
	// Répertoire des fichier de vue PHP
	define( 'VIEWDIR'	, 'view/' );
	// Répertoire des fichiers de classes PHP
	define( 'CLASSDIR'	, 'classes/' );
	// Répertoire des fichier item PHP
	define( 'ITEMDIR'	, 'items/' );
	// Répertoire des fichiers de template PHP
	define( 'TEMPLDIR'	, 'template/' );
	// Répertoire des fichiers uploadés
	define( 'UPLDIR'	, 'uploads/' );
	// Répertoire des exports CSV
	define( 'EXPDIR'	, 'exports/' );
	
	/*
	*	Constantes
	*/
	// Largeur de redimensionnement des images uploadées
	define( 'DFWIDTH'	, 50 );
	// Hauteur de redimensionnement des images uploadées
	define( 'DFHEIGHT'	, 100 );

	// Configuration BDD
	require_once( 'database.php' );
	
	/*
	*	Format de date
	*/
	// En BDD
	define( 'DBDATE' , 'Y-m-d' );
	// Affichage DATE
	define( 'UIDATE' , 'd/m/Y' );
	// Affichage DATETIME
	define( 'UIDATETIME' , 'd/m/Y H:i' );
	
	// Item page d'accueil par défaut
	define( 'HOMEPAGE', 'home' );
	
	// Sous-titre des pages
	$actions = array(
		'list' => 'Liste',
		'delete' => 'Suppression',
		'read' => 'Consultation',
		'edit' => 'Modification',
		'search' => 'Rechercher',
		'print' => 'Imprimer',
		'extract' => 'Extraction',
		'export' => 'Export',
	);
	
	/*
	*	Variables de session par défaut
	*/
	if( !isset( $_SESSION[DBPREF.'_item'] ) ) $_SESSION[DBPREF.'_item'] = 'home';
	if( !isset( $_SESSION[DBPREF.'_search'] ) ) $_SESSION[DBPREF.'_search'] = array();
	if( !isset( $_SESSION[DBPREF.'_page'] ) ) $_SESSION[DBPREF.'_page'] = '1';
	if( !isset( $_SESSION[DBPREF.'_action'] ) ) $_SESSION[DBPREF.'_action'] = 'list';
	
	/*
	*	Classes Obligatoires
	*/
	require_once( CLASSDIR.'vendor/autoload.php' );
	require_once( CLASSDIR.'Upload.php' );
	require_once( CLASSDIR.'tfpdf/tfpdf.php' );
	require_once( CLASSDIR.'Phpqrcode.php' );
	
	/*
	*	Mappings items obligatoires
	*/
	require_once( ITEMDIR.'ModelOption.php' );
	require_once( ITEMDIR.'ModelItem.php' );
	require_once( ITEMDIR.'ModelUtilisateur.php' );
	require_once( ITEMDIR.'ModelGroupe.php' );
	require_once( ITEMDIR.'ModelEtat.php' );
	require_once( ITEMDIR.'ModelAnalyse.php' );
	require_once( ITEMDIR.'ModelTypeAnalyse.php' );
	require_once( ITEMDIR.'ModelTypeDocument.php' );
	require_once( ITEMDIR.'ModelDocument.php' );
	require_once( ITEMDIR.'ModelAffectation.php' );
	require_once( ITEMDIR.'ModelColorscheme.php' );
	
	/*
	*	Mappings items personnalisés
	*/
	require_once( ITEMDIR.'ModelExemple.php' );
	
	/*
	*	Autoload de classes
	*/
	spl_autoload_register( function ($class) {
		require_once( CLASSDIR . $class . '.php' );
	});
	
