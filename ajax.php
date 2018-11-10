<?php
	require_once( 'config.php' );
	// Retour par défaut
	$retour = array(
		'message' => 'Bad Request, missing parameters',
		'item' => false,
		'data' => false,
	);
	
	if( isset( $_GET['ajaxGet'], $_GET['parent_item'] ) ) {
		$item = $_GET['parent_item'];
		$id = isset( $_GET['parent_id'] ) ? intval( $_GET['parent_id'] ) : 0;
		
		$manager = new Manager( $bdd, $debug );
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
					$retour['message'] = 'Graph values for analyse '.$id;
					$retour['item'] = $object->getItem( $id );
					$retour['data'] = $object->getDatas();
				}
				break;
			case 'distinct' :
				if( isset( $_GET['colonne'], $_GET['term'] ) ) {
					$object = new Model( $bdd, $manager, $model );
					$retour = $object->getDistinctValues( $_GET['colonne'], $_GET['term'] );
				}
				break;
		}
	}
	
	echo json_encode( $retour );
?>
