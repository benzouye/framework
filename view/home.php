<?php
	if( !$user ) {
		// On affiche le formulaire de connexion
		include( VIEWDIR.'login.php' );
	} else {
		// Sinon on affiche la page d'accueil
?>
		<p>Hello World !</p>
<?php
	}
?>
