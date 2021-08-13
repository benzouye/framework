<?php
	if( !$user ) {
		// On affiche le formulaire de connexion
		include( VIEWDIR.'login.php' );
	} else {
		// Sinon on affiche la page d'accueil
?>
				<div class="row">
<?php
		echo $manager->getOption('homeText');
		
		$analyse = new Analyse( $bdd, $manager, $model_analyse );
		$analyses = $analyse->getItems( ['flag_accueil' => 1 ], false, 1, false, false );
		
		foreach( $analyses as $element ) {
			echo $analyse->getDashboardHTML( $element );
		}
?>
				</div>
<?php
	}
