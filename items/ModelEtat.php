<?php
	$model_etat = (object) array(
		'itemName' => 'etat',
		'table' => DBPREF.'etat',
		'single' => 'état inscription',
		'plural' => 'états inscription',
		'orderby' => 'libelle',
		'columns' => array(
			(object) array(
				'name' => 'id_etat',
				'nicename' => 'ID',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'number', 'step' => 1 ),
				'visible' => false,
				'editable' => false,
				'required' => false
			),
			(object) array(
				'name' => 'libelle',
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
			)
		)
	);
?>