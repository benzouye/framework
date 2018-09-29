<?php
	$model_type_document = (object) array(
		'itemName' => 'type_document',
		'table' => DBPREF.'type_document',
		'single' => 'type de document',
		'plural' => 'types de document',
		'orderby' => 'libelle',
		'columns' => array(
			(object) array(
				'name' => 'id_type_document',
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