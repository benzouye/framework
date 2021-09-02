<?php
	$model_affectation = (object) array(
		'itemName' => 'affectation',
		'table' => DBPREF.'affectation',
		'single' => 'affectation',
		'plural' => 'affectations',
		'orderby' => 'libelle',
		'columns' => array(
			(object) array(
				'name' => 'id_affectation',
				'nicename' => 'ID',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'number', 'step' => 1 ),
				'visible' => false,
				'editable' => false,
				'required' => false,
				'admin' => false
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
				'required' => true,
				'admin' => false
			)
		)
	);
?>
