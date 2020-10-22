<?php
	// Initialisation
	session_start();
	require_once( 'config.php' );
	$manager = new Manager( $bdd, $debug );
	$user = $manager->getUser();
	$retour = array( 'message' => 'Unauthorized user' );
	
	if( $user ) {
		// Retour par défaut
		$retour = array(
			'message' => 'Bad Request, missing parameters',
			'item' => false,
			'data' => false,
		);
	
		if( isset( $_GET['ajaxGet'], $_GET['parent_item'] ) ) {
			$item = $_GET['parent_item'];
			$id = isset( $_GET['parent_id'] ) ? intval( $_GET['parent_id'] ) : 0;
			
			$model = ${'model_'.$item};
			$nomClasse = ucfirst($item);
			
			switch( $_GET['ajaxGet'] ) {
				case 'relation' :
					if( isset( $_GET['relation'] ) ) {
						$object = new $nomClasse( $bdd, $manager, $model );
						$relation = $_GET['relation'];
						$retour['message'] = 'Relation between '.$item.' and '.$relation;
						
						if( isset( $_GET['item_id'] ) ) {
							$retour['data'] = $object->{'save_'.$relation}( $_GET );
						} else {
							$retour['data'] = $object->{'get_'.$relation.'_dispo'}( $_GET );
						}
					}
					break;
				case 'analyse' :
					if( $id ) {
						$object = new $nomClasse( $bdd, $manager, $model );
						$retour['message'] = 'Values for analyse '.$id;
						$retour['options'] = $object->getItem( $id )->options;
						$retour['indicator'] = $object->getItem( $id )->indicator;
						$retour['data'] = $object->getDatas();
					} else {
						$retour['message'] = 'No analyse id was supplied';
					}
					break;
				case 'schedule' :
					if( !empty( $_GET['parent_item'] ) ) {
						$object = new $nomClasse( $bdd, $manager, $model );
						$retour = $object->get_scheduler( $_GET );
					}
					break;
				case 'distinct' :
					if( isset( $_GET['colonne'], $_GET['term'] ) ) {
						$object = new Model( $bdd, $manager, $model );
						$retour = $object->getDistinctValues( $_GET['colonne'], $_GET['term'] );
					}
					break;
				case 'option' :
					if( isset( $_GET['alias'] ) ) {
						$retour['message'] = 'Value for option '.$_GET['alias'];
						$retour['data'] = $manager->getOption( $_GET['alias'] );
					}
					break;
			}
		}
	}
	
	echo json_encode( $retour );
?>
