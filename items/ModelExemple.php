<?php
	$model_exemple = (object) array(
		'itemName' => 'exemple',
		'table' => DBPREF.'exemple',
		'single' => 'exemple',
		'plural' => 'exemples',
		'orderby' => 'date_cre DESC',
		'readOnlyStates' => array(
			(object) array(
				'item' => 'etat',
				'ids' => array( 3 ),
			),
		),
		'columns' => array(
			(object) array(
				'name' => 'id_exemple',
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
			(object) array(
				'name' => 'id_etat',
				'nicename' => 'Etat',
				'align' => 'center',
				'default' => 1,
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array(
					'type' => 'select',
					'item' => 'etat',
					'columnKey' => 'id_etat',
					'columnLabel' => 'libelle'
				),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
		),
	);
?>