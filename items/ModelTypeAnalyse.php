<?php
	$model_type_analyse = (object) array(
		'itemName' => 'type_analyse',
		'table' => DBPREF.'type_analyse',
		'single' => 'type d\'analyse',
		'plural' => 'types d\'analyse',
		'orderby' => 'id_type_analyse',
		'columns' => array(
			(object) array(
				'name' => 'id_type_analyse',
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
			),
		)
	);
?>
