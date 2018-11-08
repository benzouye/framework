<?php
	$model_item = (object) array(
		'itemName' => 'item',
		'table' => DBPREF.'item',
		'single' => 'Elément',
		'plural' => 'Eléments',
		'orderby' => 'menu ASC, menu_order ASC',
		'columns' => array(
			(object) array(
				'name' => 'id_item',
				'nicename' => 'ID',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'number' ),
				'visible' => false,
				'editable' => false,
				'required' => false
			),
			(object) array(
				'name' => 'alias',
				'nicename' => 'Alias',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 2,
				),
				'params' => array( 'type' => 'text' ),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'nom',
				'nicename' => 'Nom',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 2,
				),
				'params' => array( 'type' => 'text' ),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'description',
				'nicename' => 'Libellé',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'type' => 'text' ),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'static',
				'nicename' => 'Statique ?',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'checkbox' ),
				'visible' => true,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'variant',
				'nicename' => 'Complexe ?',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'checkbox' ),
				'visible' => true,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'active',
				'nicename' => 'Actif ?',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'checkbox' ),
				'visible' => true,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'admin',
				'nicename' => 'Admin ?',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'checkbox' ),
				'visible' => true,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'menu',
				'nicename' => 'Menu',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'number' ),
				'visible' => true,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'menu_order',
				'nicename' => 'Ordre',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'number' ),
				'visible' => true,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'icon',
				'nicename' => 'Icône',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 2,
				),
				'params' => array( 'type' => 'text' ),
				'visible' => true,
				'editable' => true,
				'required' => false
			)
		),
	);
?>
