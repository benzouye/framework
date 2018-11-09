<?php
	require_once( 'config.php' );
	$items = array();
	
	if( isset( $_GET['parent_item'] ) ) {
		$manager = new Manager( $bdd, $debug );
		$model = ${'model_'.$_GET['parent_item']};
		$nomClasse = ucfirst($_GET['parent_item']);
		$object = new $nomClasse( $bdd, $manager, $model );
			
		if( isset( $_GET['parent_id'] ) && $_GET['parent_item'] == 'analyse' ) {
			$item = $object->getItem( intval($_GET['parent_id']) );
			if( isset( $_GET['object'] ) ) {
				$items['object'] = $item;
				$items['data'] = $object->getDatas();
			} else {
				$items = $object->getDatas();
			}
		}
		
		if( isset( $_GET['relation'] ) ) {
			
			if( intval( $_GET['parent_id'] ) )
				$item = $object->getItem( intval( $_GET['parent_id'] ) );
			
			if( isset( $_GET['item_id'] ) ) {
				$items = $object->{'save_'.$_GET['relation']}( $_GET );
			} else {
				$items = $object->{'get_'.$_GET['relation'].'_dispo'}( $_GET );
			}
			
		}
		
		if( isset( $_GET['schedule'] ) ) {
			$items = $object->get_scheduler( $_GET );
		}
	}
	
	echo json_encode( $items );
?>
