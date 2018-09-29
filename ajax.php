<?php
	require_once( 'config.php' );
	
	$nomClasse = ucfirst($_GET['parent_item']);
	
	if( isset( $_GET['relation'] ) ) {
		$manager = new Manager( $bdd, $debug );
		$model = ${'model_'.$_GET['parent_item']};	
		$object = new $nomClasse( $bdd, $manager, $model );
		
		if( intval( $_GET['parent_id'] ) )
			$item = $object->getItem( $_GET['parent_id'] );
		
		if( isset( $_GET['item_id'] ) ) {
			$items = $object->{'save_'.$_GET['relation']}( $_GET );
		} else {
			$items = $object->{'get_'.$_GET['relation'].'_dispo'}( $_GET );
		}
	} elseif( isset( $_GET['schedule'] ) ) {
		$manager = new Manager( $bdd, $debug );
		$model = ${'model_'.$_GET['parent_item']};	
		$object = new $nomClasse( $bdd, $manager, $model );
		$items = $object->get_scheduler( $_GET );
	} else {
		$items = array();
	}
	
	echo json_encode( $items );
?>