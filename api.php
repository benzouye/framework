<?php
	// Initialisation
	session_start();
	require_once( 'config.php' );
	$manager = new Manager( $bdd, $debug );
	$user = $manager->getUser();
	$retour = array( 'message' => 'Unauthorized user' );
	
	// Authentification OK
	if( $user ) {
		
		if( !empty( $_POST['id'] ) ) {
			$id = intval( $_POST['id'] );
			$analyse = new Analyse( $bdd, $manager, $model_analyse );
			
			$retour = array(
				'message' => 'Datas for custom API '.$id,
				'data' => $analyse->getDatas();,
			);
		} else {
			$retour = array(
				'message' => 'Bad Request, missing parameters',
				'data' => false,
			);
		}
	}
	
	echo json_encode( $retour );
?>
