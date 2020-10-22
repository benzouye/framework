<?php
	// Initialisation
	session_start();
	require_once( 'config.php' );
	$manager = new Manager( $bdd, $debug );
	$user = $manager->getUser();
	$retour = array(
		'message' => 'Unauthorized user',
		'data' => false
	);
	
	// Authentification OK
	if( $user ) {
		
		if( !empty( $_POST['id'] ) ) {
			$Analyse = new Analyse( $bdd, $manager, $model_analyse );
			$item = $Analyse->getItem( intval( $_POST['id'] ) );
			
			if( $item->description ) {
				$retour['message'] = $item->description;
				$retour['data'] = $Analyse->getDatas();
			} else {
				$retour['message'] = 'This API ID does not exist';
			}
		} else {
			$retour['message'] = 'Missing POST parameter';
		}
	}
	
	echo json_encode( $retour );
?>
