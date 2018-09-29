<?php
	$model_option = (object) array(
		'itemName' => 'option',
		'table' => DBPREF.'option',
		'single' => 'option',
		'plural' => 'options',
		'orderby' => 'alias',
		'columns' => array(
			(object) array(
				'name' => 'id_option',
				'nicename' => 'ID',
				'params' => array( 'type' => 'number', 'step' => 1 ),
				'visible' => false,
				'editable' => false,
				'required' => true
			),
			(object) array(
				'name' => 'alias',
				'nicename' => 'Alias',
				'params' => array( 'type' => 'text' ),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'description',
				'nicename' => 'Description',
				'params' => array( 'type' => 'text' ),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'valeur',
				'nicename' => 'Valeur',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 10,
				),
				'params' => array( 'type' => 'textarea', 'rows' => 10 ),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
		),
	);
?>