<?php
	// Mise en tampon
	ob_start();
	
	// Gestion de session
	session_start();
	
	// Initialisation du framework
	require_once( 'config.php' );
	
	// Contrôleur
	require_once( 'controller.php' );
	
	// Affichage du header HTML si pas impression
	if( $action != 'print' ) {
		require_once( TEMPLDIR.'header.php' );
	}
	
	// Affichage du template donné par le contrôleur si il existe
	if( $template ) {
		include( $template );
	}
		
	// Affichage du footer HTML si pas impression
	if( $action != 'print' ) {
		require_once( TEMPLDIR.'footer.php' );
	}
	
	// Libération tampon d'affichage
	ob_end_flush();
?>
