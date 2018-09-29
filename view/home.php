<?php
	if( !$user ) {
		// On affiche le formulaire de connexion
		include( VIEWDIR.'login.php' );
	} else {
		// Sinon on affiche la page d'accueil
		echo '<p>Hello World !</p>';
	}
?>
